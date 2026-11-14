<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageOptimizer
{
    private const int MAX_SOURCE_DIMENSION = 6000;

    private const int MAX_SOURCE_PIXELS = 24_000_000;

    private const int MAX_OUTPUT_DIMENSION = 1600;

    private const int WEBP_QUALITY = 82;

    public function store(UploadedFile $image): string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return $image->store('products', 'public');
        }

        $imageInformation = getimagesize($image->getPathname());

        if (! is_array($imageInformation)) {
            return $image->store('products', 'public');
        }

        [$width, $height] = $imageInformation;

        if (
            $width < 1
            || $height < 1
            || $width > self::MAX_SOURCE_DIMENSION
            || $height > self::MAX_SOURCE_DIMENSION
            || $width * $height > self::MAX_SOURCE_PIXELS
        ) {
            return $image->store('products', 'public');
        }

        $source = imagecreatefromstring($image->get());

        if (! $source instanceof \GdImage) {
            return $image->store('products', 'public');
        }

        $scale = min(1, self::MAX_OUTPUT_DIMENSION / max($width, $height));
        $outputWidth = max(1, (int) round($width * $scale));
        $outputHeight = max(1, (int) round($height * $scale));
        $optimized = imagecreatetruecolor($outputWidth, $outputHeight);

        if (! $optimized instanceof \GdImage) {
            imagedestroy($source);

            return $image->store('products', 'public');
        }

        imagealphablending($optimized, false);
        imagesavealpha($optimized, true);
        imagecopyresampled($optimized, $source, 0, 0, 0, 0, $outputWidth, $outputHeight, $width, $height);

        ob_start();
        $encoded = imagewebp($optimized, null, self::WEBP_QUALITY);
        $optimizedImage = ob_get_clean();

        imagedestroy($source);
        imagedestroy($optimized);

        if (! $encoded || ! is_string($optimizedImage) || $optimizedImage === '') {
            return $image->store('products', 'public');
        }

        $path = 'products/'.Str::uuid().'.webp';
        if (! Storage::disk('public')->put($path, $optimizedImage)) {
            return $image->store('products', 'public');
        }

        return $path;
    }
}
