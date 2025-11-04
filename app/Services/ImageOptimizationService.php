<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Image Optimization Service
 *
 * Provides image optimization for helpdesk ticket attachments using PHP GD library.
 * Features: WebP conversion, JPEG fallbacks, thumbnail generation, responsive sizes.
 *
 * @see D11 Technical Design Documentation - Performance Optimization
 * @see D04 Software Design Document - Image Optimization
 * @see Requirements 9.2 - Image optimization requirements
 */
class ImageOptimizationService
{
    protected array $sizes = [
        'thumbnail' => ['width' => 150, 'height' => 150],
        'medium' => ['width' => 800, 'height' => 600],
        'large' => ['width' => 1920, 'height' => 1080],
    ];

    protected array $supportedMimeTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Optimize uploaded image file
     *
     * @return array{original: string, webp: string|null, thumbnail: string|null, sizes: array}
     */
    public function optimizeImage(UploadedFile $file, string $directory = 'attachments'): array
    {
        if (! $this->isImage($file)) {
            throw new \InvalidArgumentException('File must be an image');
        }

        $filename = $this->generateFilename($file);
        $extension = $file->getClientOriginalExtension();

        // Store original
        $originalPath = $file->storeAs($directory, $filename.'.'.$extension, 'private');

        $result = [
            'original' => $originalPath,
            'webp' => null,
            'thumbnail' => null,
            'sizes' => [],
        ];

        try {
            $sourceImage = $this->createImageFromFile($file);
            if ($sourceImage === false) {
                return $result;
            }

            $basePath = $directory.'/'.$filename;
            $result['webp'] = $this->generateWebP($sourceImage, $basePath);
            $result['thumbnail'] = $this->generateThumbnail($sourceImage, $basePath);
            $result['sizes'] = $this->generateResponsiveSizes($sourceImage, $basePath);

            imagedestroy($sourceImage);
        } catch (\Exception $e) {
            Log::error('Image optimization failed', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
        }

        return $result;
    }

    /**
     * @return \GdImage|false
     */
    protected function createImageFromFile(UploadedFile $file)
    {
        return match ($file->getMimeType()) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($file->getRealPath()),
            'image/png' => imagecreatefrompng($file->getRealPath()),
            'image/gif' => imagecreatefromgif($file->getRealPath()),
            'image/webp' => imagecreatefromwebp($file->getRealPath()),
            default => false,
        };
    }

    protected function generateWebP($sourceImage, string $basePath): ?string
    {
        if (! function_exists('imagewebp')) {
            return null;
        }

        try {
            $webpPath = $basePath.'.webp';
            $tempPath = storage_path('app/temp/'.Str::uuid().'.webp');

            if (! is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            imagewebp($sourceImage, $tempPath, 85);
            Storage::disk('private')->put($webpPath, file_get_contents($tempPath));
            unlink($tempPath);

            return $webpPath;
        } catch (\Exception $e) {
            Log::warning('WebP generation failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    protected function generateThumbnail($sourceImage, string $basePath): ?string
    {
        try {
            $thumbnailPath = $basePath.'_thumbnail.jpg';
            $tempPath = storage_path('app/temp/'.Str::uuid().'.jpg');

            if (! is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            $thumbnail = $this->resizeImageCover(
                $sourceImage,
                $this->sizes['thumbnail']['width'],
                $this->sizes['thumbnail']['height']
            );

            imagejpeg($thumbnail, $tempPath, 80);
            imagedestroy($thumbnail);

            Storage::disk('private')->put($thumbnailPath, file_get_contents($tempPath));
            unlink($tempPath);

            return $thumbnailPath;
        } catch (\Exception $e) {
            Log::warning('Thumbnail generation failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @return array<string, string>
     */
    protected function generateResponsiveSizes($sourceImage, string $basePath): array
    {
        $sizes = [];

        foreach (['medium', 'large'] as $sizeName) {
            try {
                $sizePath = $basePath.'_'.$sizeName.'.jpg';
                $tempPath = storage_path('app/temp/'.Str::uuid().'.jpg');

                if (! is_dir(dirname($tempPath))) {
                    mkdir(dirname($tempPath), 0755, true);
                }

                $resized = $this->resizeImageContain(
                    $sourceImage,
                    $this->sizes[$sizeName]['width'],
                    $this->sizes[$sizeName]['height']
                );

                imagejpeg($resized, $tempPath, 85);
                imagedestroy($resized);

                Storage::disk('private')->put($sizePath, file_get_contents($tempPath));
                unlink($tempPath);

                $sizes[$sizeName] = $sizePath;
            } catch (\Exception $e) {
                Log::warning("Failed to generate {$sizeName} size", ['error' => $e->getMessage()]);
            }
        }

        return $sizes;
    }

    /**
     * @return \GdImage
     */
    protected function resizeImageCover($sourceImage, int $targetWidth, int $targetHeight)
    {
        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $newHeight = $targetHeight;
            $newWidth = (int) ($targetHeight * $sourceRatio);
        } else {
            $newWidth = $targetWidth;
            $newHeight = (int) ($targetWidth / $sourceRatio);
        }

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled(
            $resized,
            $sourceImage,
            -($newWidth - $targetWidth) / 2,
            -($newHeight - $targetHeight) / 2,
            0,
            0,
            $newWidth,
            $newHeight,
            $sourceWidth,
            $sourceHeight
        );

        return $resized;
    }

    /**
     * @return \GdImage
     */
    protected function resizeImageContain($sourceImage, int $maxWidth, int $maxHeight)
    {
        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        if ($sourceWidth <= $maxWidth && $sourceHeight <= $maxHeight) {
            $newWidth = $sourceWidth;
            $newHeight = $sourceHeight;
        } else {
            $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
            $newWidth = (int) ($sourceWidth * $ratio);
            $newHeight = (int) ($sourceHeight * $ratio);
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled(
            $resized,
            $sourceImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $sourceWidth,
            $sourceHeight
        );

        return $resized;
    }

    public function isImage(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), $this->supportedMimeTypes, true);
    }

    protected function generateFilename(UploadedFile $file): string
    {
        return Str::uuid()->toString();
    }

    /**
     * @return array{src: string, srcset: string, sizes: string, loading: string, fetchpriority: string}
     */
    public function getImageAttributes(array $optimizedPaths, bool $isPriority = false): array
    {
        $attributes = [
            'loading' => $isPriority ? 'eager' : 'lazy',
            'fetchpriority' => $isPriority ? 'high' : 'auto',
            'src' => $optimizedPaths['webp'] ?? $optimizedPaths['original'],
        ];

        $srcset = [];
        if (isset($optimizedPaths['sizes']['medium'])) {
            $srcset[] = Storage::disk('private')->url($optimizedPaths['sizes']['medium']).' 800w';
        }
        if (isset($optimizedPaths['sizes']['large'])) {
            $srcset[] = Storage::disk('private')->url($optimizedPaths['sizes']['large']).' 1920w';
        }

        if (! empty($srcset)) {
            $attributes['srcset'] = implode(', ', $srcset);
            $attributes['sizes'] = '(max-width: 800px) 100vw, (max-width: 1920px) 800px, 1920px';
        }

        return $attributes;
    }

    public function deleteOptimizedImages(array $optimizedPaths): void
    {
        $disk = Storage::disk('private');

        foreach ($optimizedPaths as $key => $path) {
            if (is_array($path)) {
                foreach ($path as $subPath) {
                    if ($disk->exists($subPath)) {
                        $disk->delete($subPath);
                    }
                }
            } elseif ($path && $disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }

    public function getThumbnailUrl(array $optimizedPaths): ?string
    {
        if (isset($optimizedPaths['thumbnail'])) {
            return Storage::disk('private')->url($optimizedPaths['thumbnail']);
        }

        return null;
    }

    public function getOptimizedUrl(array $optimizedPaths): string
    {
        if (isset($optimizedPaths['webp'])) {
            return Storage::disk('private')->url($optimizedPaths['webp']);
        }

        return Storage::disk('private')->url($optimizedPaths['original']);
    }
}
