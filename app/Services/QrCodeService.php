<?php

declare(strict_types=1);

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * QR Code Generation Service
 *
 * Generates QR codes for ticket/loan status tracking and asset identification.
 *
 * @trace D12 §6.15 (Print Optimization)
 *
 * @requirements 20.5 (QR Code Display)
 */
class QrCodeService
{
    /**
     * Default QR code size in pixels
     */
    private const DEFAULT_SIZE = 200;

    /**
     * Generate a QR code SVG for a ticket status lookup URL
     *
     * @param  string  $ticketNumber  The ticket number to encode
     * @param  int  $size  QR code size in pixels
     * @return string SVG markup
     */
    public function generateTicketQrCode(string $ticketNumber, int $size = self::DEFAULT_SIZE): string
    {
        $url = route('status.check', ['type' => 'helpdesk', 'reference' => $ticketNumber]);

        return $this->generateSvg($url, $size);
    }

    /**
     * Generate a QR code SVG for a loan application status lookup URL
     *
     * @param  string  $applicationNumber  The loan application number to encode
     * @param  int  $size  QR code size in pixels
     * @return string SVG markup
     */
    public function generateLoanQrCode(string $applicationNumber, int $size = self::DEFAULT_SIZE): string
    {
        $url = route('status.check', ['type' => 'loan', 'reference' => $applicationNumber]);

        return $this->generateSvg($url, $size);
    }

    /**
     * Generate a QR code SVG for an asset tag lookup URL
     *
     * @param  string  $assetTag  The asset tag to encode
     * @param  int  $size  QR code size in pixels
     * @return string SVG markup
     */
    public function generateAssetQrCode(string $assetTag, int $size = self::DEFAULT_SIZE): string
    {
        // Asset lookup URL - adjust route name as needed
        $url = url('/assets/lookup/'.urlencode($assetTag));

        return $this->generateSvg($url, $size);
    }

    /**
     * Generate a QR code SVG for any URL
     *
     * @param  string  $url  The URL to encode
     * @param  int  $size  QR code size in pixels
     * @return string SVG markup
     */
    public function generateSvg(string $url, int $size = self::DEFAULT_SIZE): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd
        );

        $writer = new Writer($renderer);

        return $writer->writeString($url);
    }

    /**
     * Generate a QR code as a data URI for embedding in HTML/PDF
     *
     * @param  string  $url  The URL to encode
     * @param  int  $size  QR code size in pixels
     * @return string Data URI (data:image/svg+xml;base64,...)
     */
    public function generateDataUri(string $url, int $size = self::DEFAULT_SIZE): string
    {
        $svg = $this->generateSvg($url, $size);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Generate a QR code for ticket status and return as data URI
     *
     * @param  string  $ticketNumber  The ticket number
     * @param  int  $size  QR code size in pixels
     * @return string Data URI
     */
    public function getTicketQrCodeDataUri(string $ticketNumber, int $size = self::DEFAULT_SIZE): string
    {
        $url = route('status.check', ['type' => 'helpdesk', 'reference' => $ticketNumber]);

        return $this->generateDataUri($url, $size);
    }

    /**
     * Generate a QR code for loan status and return as data URI
     *
     * @param  string  $applicationNumber  The loan application number
     * @param  int  $size  QR code size in pixels
     * @return string Data URI
     */
    public function getLoanQrCodeDataUri(string $applicationNumber, int $size = self::DEFAULT_SIZE): string
    {
        $url = route('status.check', ['type' => 'loan', 'reference' => $applicationNumber]);

        return $this->generateDataUri($url, $size);
    }

    /**
     * Generate a QR code for asset lookup and return as data URI
     *
     * @param  string  $assetTag  The asset tag
     * @param  int  $size  QR code size in pixels
     * @return string Data URI
     */
    public function getAssetQrCodeDataUri(string $assetTag, int $size = self::DEFAULT_SIZE): string
    {
        $url = url('/assets/lookup/'.urlencode($assetTag));

        return $this->generateDataUri($url, $size);
    }
}
