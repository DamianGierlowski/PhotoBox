<?php

namespace App\Service\Files;

class WatermarkingService
{

    public function addWatermark(string $sourceImagePath, string $watermarkText, string $outputPath): void
    {
        if (!file_exists($sourceImagePath)) {
            throw new \Exception('Source image file does not exist.');
        }

        // Load the source image
        $image = imagecreatefromjpeg($sourceImagePath);
        if (!$image) {
            throw new \Exception('Failed to load the source image.');
        }

        // Get the image dimensions
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        // Create a semi-transparent color for the watermark
        $watermarkColor = imagecolorallocatealpha($image, 255, 255, 255, 85); // White with transparency

        // Define font size and calculate placement
        $fontFile = __DIR__ . '/fonts/arial.ttf'; // Replace with a valid TTF font file path
        if (!file_exists($fontFile)) {
            throw new \Exception('Font file does not exist. Please provide a valid font file.');
        }
        $fontSize = 60;

        // Tile the watermark across the image
        $padding = 500; // Distance between watermark repetitions
        for ($y = 0; $y < $imageHeight; $y += $padding) {
            for ($x = 0; $x < $imageWidth; $x += $padding) {
                imagettftext($image, $fontSize, 45, $x, $y, $watermarkColor, $fontFile, $watermarkText);
            }
        }

        // Save the output image
        if (!imagejpeg($image, $outputPath)) {
            imagedestroy($image);
            throw new \Exception('Failed to save the watermarked image.');
        }

        // Free up memory
        imagedestroy($image);
    }


}