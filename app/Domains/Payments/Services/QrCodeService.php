<?php

namespace App\Domains\Payments\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    /**
     * Generate a QR code image from KHQR payload as base64 PNG data.
     *
     * @param string $payload The KHQR payload string
     * @param int $size The QR code size in pixels (default: 400)
     * @return string Base64-encoded PNG image data (data URI format)
     */
    public function generateQrCodeFromPayload(string $payload, int $size = 400): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $payload,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: $size,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        $result = $builder->build();
        $imageData = $result->getString();

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    /**
     * Generate a QR code image from KHQR payload and return as PNG binary.
     *
     * @param string $payload The KHQR payload string
     * @param int $size The QR code size in pixels (default: 400)
     * @return string Raw PNG binary data
     */
    public function generateQrCodeBinary(string $payload, int $size = 400): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $payload,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: $size,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        $result = $builder->build();

        return $result->getString();
    }
}
