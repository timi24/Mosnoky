<?php

use App\Services\ProductImageColorIdentifier;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

test('it identifies an uploaded product photo using one of the available colors', function () {
    config()->set('services.openai.api_key', 'test-api-key');
    Http::preventStrayRequests();
    Http::fake([
        'api.openai.com/v1/responses' => Http::response([
            'output' => [[
                'type' => 'message',
                'content' => [[
                    'type' => 'output_text',
                    'text' => '{"color":"Rose"}',
                ]],
            ]],
        ]),
    ]);

    $color = app(ProductImageColorIdentifier::class)->identify(
        UploadedFile::fake()->image('sac.jpg'),
        ['Rose', 'Noir'],
    );

    expect($color)->toBe('Rose');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.openai.com/v1/responses'
        && $request->hasHeader('Authorization', 'Bearer test-api-key')
        && $request['input'][0]['content'][1]['type'] === 'input_image'
        && $request['text']['format']['schema']['properties']['color']['enum'] === ['Rose', 'Noir', null]
    );
});

test('it skips automatic color identification when no OpenAI key is configured', function () {
    config()->set('services.openai.api_key', null);
    Http::preventStrayRequests();

    $color = app(ProductImageColorIdentifier::class)->identify(
        UploadedFile::fake()->image('sac.jpg'),
        ['Rose', 'Noir'],
    );

    expect($color)->toBeNull();

    Http::assertNothingSent();
});
