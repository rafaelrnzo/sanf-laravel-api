<?php

namespace Sanf\Api\Modules\LlmOcr\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use RuntimeException;
use Sanf\Core\Modules\LlmOcr\Enums\DocumentTypeEnum;
use Sanf\Core\Modules\LlmOcr\Services\GeminiOcrService;
use Throwable;

class LlmOcrController extends RestApiController
{
    public function extract(Request $request, GeminiOcrService $service)
    {
        $input = $this->validate($request, [
            'file_content_base64' => ['required', 'string'],
            'mime_type' => ['required', Rule::in([
                'application/pdf',
                'image/png',
                'image/jpeg',
            ])],
            'document_type' => [
                'required_without:prompt',
                'nullable',
                Rule::in(array_column(DocumentTypeEnum::cases(), 'value')),
            ],
            'prompt' => ['required_without:document_type', 'nullable', 'string'],
        ]);

        $fileContent = base64_decode($input['file_content_base64'], true);
        if ($fileContent === false) {
            return response()->json([
                'success' => false,
                'message' => 'The file content must be valid base64.',
            ], 422);
        }

        $maxBytes = (int) config('llm-ocr.max_file_size', 10240) * 1024;
        if (strlen($fileContent) > $maxBytes) {
            return response()->json([
                'success' => false,
                'message' => 'The file may not be greater than ' . config('llm-ocr.max_file_size', 10240) . ' kilobytes.',
            ], 422);
        }

        $documentType = isset($input['document_type'])
            ? DocumentTypeEnum::from($input['document_type'])
            : null;

        try {
            $result = $service->extract(
                $fileContent,
                $input['mime_type'],
                $documentType,
                $input['prompt'] ?? null
            );
        } catch (Throwable $exception) {
            report($exception);

            $message = $exception instanceof RuntimeException
                ? $exception->getMessage()
                : 'Unexpected OCR error.';

            return response()->json([
                'success' => false,
                'message' => 'OCR gagal: ' . $message,
            ], 500);
        }

        return response()->json($result);
    }
}
