<?php

use App\Services\ProductImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

test('it stores uploaded product photos as resized webp images', function () {
    Storage::fake('public');

    $image = UploadedFile::fake()->image('product.jpg', 2400, 1200);
    $path = app(ProductImageOptimizer::class)->store($image);
    $storedImage = Storage::disk('public')->get($path);
    $imageInformation = getimagesizefromstring($storedImage);

    expect($path)->toEndWith('.webp')
        ->and($imageInformation[0])->toBe(1600)
        ->and($imageInformation[1])->toBe(800);
});
test('example', function () {
    expect(true)->toBeTrue();
});
