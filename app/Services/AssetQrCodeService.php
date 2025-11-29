<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Collection;

class AssetQrCodeService
{
    /**
     * Generate QR code SVG for an asset
     */
    public function generateQrCode(Asset $asset): string
    {
        $url = route('assets.view', ['asset' => $asset->id]);

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return $writer->writeString($url);
    }

    /**
     * Generate QR codes for multiple assets
     *
     * @param  Collection<int, Asset>  $assets
     * @return array<int, string>
     */
    public function generateBulkQrCodes(Collection $assets): array
    {
        return $assets->mapWithKeys(function (Asset $asset) {
            return [$asset->id => $this->generateQrCode($asset)];
        })->all();
    }

    /**
     * Generate printable QR code labels (HTML for printing)
     *
     * @param  Collection<int, Asset>  $assets
     */
    public function generatePrintableLabels(Collection $assets): string
    {
        $html = '<html><head><style>
            .label { page-break-inside: avoid; display: inline-block; margin: 10px; text-align: center; }
            .qr-code { width: 150px; height: 150px; }
            .asset-info { margin-top: 5px; font-size: 12px; }
        </style></head><body>';

        foreach ($assets as $asset) {
            $qrCode = $this->generateQrCode($asset);
            $html .= '<div class="label">';
            $html .= '<div class="qr-code">' . $qrCode . '</div>';
            $html .= '<div class="asset-info">';
            $html .= '<strong>' . htmlspecialchars($asset->asset_tag) . '</strong><br>';
            $html .= htmlspecialchars($asset->name);
            $html .= '</div></div>';
        }

        $html .= '</body></html>';

        return $html;
    }
}
