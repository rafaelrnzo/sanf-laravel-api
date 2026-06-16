<?php

namespace Sanf\Integration\Modules\Gemini;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class LlmOcrClient
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com/',
            'timeout'  => 30.0,
        ]);
    }

    /**
     * @param string $mimeType
     * @param string $base64Data
     * @param string|null $apiKey
     * @return string
     * @throws \Exception
     */
    public function extractTextFromBase64(string $mimeType, string $base64Data, ?string $apiKey = null): string
    {
        $key = $apiKey ?: config('services.gemini.api_key', env('GEMINI_API_KEY'));

        if (!$key) {
            throw new \Exception('Gemini API key is not configured.');
        }

        try {
            $response = $this->client->post("v1beta/models/gemini-1.5-flash:generateContent?key={$key}", [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => 'Extract the following information from the document: nomor_invoice (Invoice Number), total_invoice (Total Amount), and no_plafond (Plafond Number). If you cannot find a specific field, leave it null. Return the result strictly as a valid JSON object without markdown formatting, with the keys: nomor_invoice, total_invoice, no_plafond.'
                                ],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $base64Data
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            return $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
        } catch (GuzzleException $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            throw new \Exception('Failed to extract text from document using LLM.');
        }
    }
}
