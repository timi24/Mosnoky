<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use JsonException;

class ProductImageColorIdentifier
{
    /**
     * @param  array<int, string>  $availableColors
     */
    public function identify(UploadedFile $image, array $availableColors): ?string
    {
        $apiKey = config('services.openai.api_key');
        $availableColors = array_values(array_unique(array_filter($availableColors, 'is_string')));

        if (! is_string($apiKey) || $apiKey === '' || $availableColors === []) {
            return null;
        }

        $mimeType = $image->getMimeType();

        if (! is_string($mimeType) || ! str_starts_with($mimeType, 'image/')) {
            return null;
        }

        $dataUrl = 'data:'.$mimeType.';base64,'.base64_encode($image->get());

        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->connectTimeout(5)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.vision_model', 'gpt-4o-mini'),
                    'max_output_tokens' => 50,
                    'input' => [[
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => 'Identifie la couleur dominante du produit au premier plan. Ignore le fond, les ombres, les logos et les petites pièces métalliques. Choisis exactement une couleur dans cette liste : '.implode(', ', $availableColors).'. Si aucune couleur ne correspond clairement, réponds null.',
                            ],
                            [
                                'type' => 'input_image',
                                'image_url' => $dataUrl,
                                'detail' => 'high',
                            ],
                        ],
                    ]],
                    'text' => [
                        'format' => [
                            'type' => 'json_schema',
                            'name' => 'product_color',
                            'strict' => true,
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'color' => [
                                        'type' => ['string', 'null'],
                                        'enum' => [...$availableColors, null],
                                    ],
                                ],
                                'required' => ['color'],
                                'additionalProperties' => false,
                            ],
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('La détection automatique de la couleur a échoué.', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $outputText = $this->outputText($response->json('output', []));
            $result = json_decode($outputText, true, flags: JSON_THROW_ON_ERROR);
            $color = $result['color'] ?? null;

            return is_string($color) && in_array($color, $availableColors, true)
                ? $color
                : null;
        } catch (ConnectionException|JsonException $exception) {
            Log::warning('La détection automatique de la couleur est indisponible.', [
                'exception' => $exception::class,
            ]);

            return null;
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $output
     */
    private function outputText(array $output): string
    {
        foreach ($output as $item) {
            foreach ($item['content'] ?? [] as $content) {
                if (($content['type'] ?? null) === 'output_text' && is_string($content['text'] ?? null)) {
                    return $content['text'];
                }
            }
        }

        return '';
    }
}
