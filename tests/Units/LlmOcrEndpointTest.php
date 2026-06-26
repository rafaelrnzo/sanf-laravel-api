<?php

use Illuminate\Support\Facades\Http;

class LlmOcrEndpointTest extends TestCase
{
    public function test_it_extracts_invoice_data_through_gemini(): void
    {
        config([
            'llm-ocr.api_key' => 'internal-secret',
            'llm-ocr.gemini.api_key' => 'gemini-secret',
            'llm-ocr.gemini.model' => 'gemini-2.5-flash',
            'llm-ocr.gemini.base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => "```json\n{\"invoice_number\":\"INV-001\",\"invoice_date\":\"2026-06-25\",\"subtotal\":1000,\"grand_total\":1000}\n```",
                        ]],
                    ],
                ]],
            ]),
        ]);

        $this->json('POST', '/api/ocr/extract', [
            'file_content_base64' => base64_encode('%PDF-test'),
            'mime_type' => 'application/pdf',
            'document_type' => 'invoice',
        ], [
            'X-API-Key' => 'internal-secret',
        ]);

        $this->assertResponseOk();
        $this->seeJson([
            'invoice_number' => 'INV-001',
            'invoice_date' => '2026-06-25',
            'subtotal' => 1000,
            'grand_total' => 1000,
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'gemini-2.5-flash:generateContent?key=gemini-secret')
                && $request['contents'][0]['parts'][0]['inline_data']['mime_type'] === 'application/pdf'
                && $request['contents'][0]['parts'][0]['inline_data']['data'] === base64_encode('%PDF-test')
                && str_contains($request['contents'][0]['parts'][1]['text'], 'invoice_number');
        });
    }

    public function test_it_rejects_an_invalid_api_key(): void
    {
        config(['llm-ocr.api_key' => 'internal-secret']);

        $this->json('POST', '/api/ocr/extract', [], [
            'X-API-Key' => 'wrong-secret',
        ]);

        $this->assertResponseStatus(401);
        $this->seeJson([
            'success' => false,
            'message' => 'Unauthorized.',
        ]);
    }
}
