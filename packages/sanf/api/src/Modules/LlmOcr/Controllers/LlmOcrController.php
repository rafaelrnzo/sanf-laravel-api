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
        $maxKb = (int) config('llm-ocr.max_file_size', 10240);

        $rules = [
            'document_type' => [
                'required_without:prompt',
                'nullable',
                Rule::in(array_column(DocumentTypeEnum::cases(), 'value')),
            ],
            'prompt' => ['required_without:document_type', 'nullable', 'string'],
            'multiple' => ['sometimes', 'boolean'],
        ];

        if ($request->hasFile('file')) {
            $rules['file'] = [
                'required',
                'file',
                'mimetypes:application/pdf,image/png,image/jpeg',
                'max:' . $maxKb,
            ];
        } else {
            $rules['file_content_base64'] = ['required', 'string'];
            $rules['mime_type'] = [
                'required',
                Rule::in([
                    'application/pdf',
                    'image/png',
                    'image/jpeg',
                ])
            ];
        }

        $input = $this->validate($request, $rules);

        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $fileContent = file_get_contents($uploaded->getRealPath());
            $mimeType = $uploaded->getMimeType();
        } else {
            $fileContent = base64_decode($input['file_content_base64'], true);
            if ($fileContent === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'The file content must be valid base64.',
                ], 422);
            }

            if (strlen($fileContent) > $maxKb * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'The file may not be greater than ' . $maxKb . ' kilobytes.',
                ], 422);
            }

            $mimeType = $input['mime_type'];
        }

        $documentType = isset($input['document_type'])
            ? DocumentTypeEnum::from($input['document_type'])
            : null;

        $multiple = $request->boolean('multiple', true);

        try {
            $result = $service->extract(
                $fileContent,
                $mimeType,
                $documentType,
                $input['prompt'] ?? null,
                $multiple
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
