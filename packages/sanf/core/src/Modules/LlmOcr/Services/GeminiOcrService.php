<?php

namespace Sanf\Core\Modules\LlmOcr\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Sanf\Core\Modules\LlmOcr\Enums\DocumentTypeEnum;

class GeminiOcrService
{
    public function extract(
        string $fileContent,
        string $mimeType,
        ?DocumentTypeEnum $documentType = null,
        ?string $prompt = null,
        bool $multiple = false
    ): array {
        if (! $prompt && $documentType) {
            $prompt = $multiple
                ? $documentType->extractionPromptMultiple()
                : $documentType->extractionPrompt();
        }

        if (! $prompt) {
            throw new RuntimeException('OCR extraction prompt is not configured.');
        }

        $apiKey = (string) config('llm-ocr.gemini.api_key');
        if ($apiKey === '') {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $baseUrl = rtrim((string) config('llm-ocr.gemini.base_url'), '/');
        $model = (string) config('llm-ocr.gemini.model');
        $endpoint = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::withOptions([
            'connect_timeout' => (int) config('llm-ocr.gemini.connect_timeout', 15),
        ])
            ->timeout((int) config('llm-ocr.gemini.timeout', 60))
            ->acceptJson()
            ->asJson()
            ->post($endpoint, [
                'contents' => [[
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => base64_encode($fileContent),
                            ],
                        ],
                        ['text' => $prompt],
                    ],
                ]],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if (! $response->successful()) {
            Log::error('Gemini OCR API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Gemini OCR API error: ' . $response->status());
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini OCR: empty response from model.');
        }

        $cleaned = trim($text);
        if (str_starts_with($cleaned, '```')) {
            $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
            $cleaned = preg_replace('/\s*```$/', '', $cleaned);
        }

        $parsed = json_decode($cleaned, true);
        if (! is_array($parsed) || json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Gemini OCR: failed to parse JSON', ['raw' => $text]);

            throw new RuntimeException('Gemini OCR: invalid JSON response.');
        }

        if (! $multiple) {
            return $parsed;
        }

        // Multi-document mode: normalise to a list and validate each item.
        $items = array_is_list($parsed) ? $parsed : [$parsed];

        $required = $documentType ? $documentType->requiredFields() : [];

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                Log::error('Gemini OCR: non-object item in multi-document response', ['raw' => $text]);

                throw new RuntimeException('Gemini OCR: invalid item in multi-document response.');
            }

            $missing = array_diff($required, array_keys($item));
            if ($missing) {
                Log::warning('Gemini OCR: item missing required fields', [
                    'index' => $index,
                    'missing' => array_values($missing),
                ]);
            }
        }

        return $items;
    }
}
