<?php

namespace Sanf\Api\Modules\Ocr\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Ocr\Transformers\OcrExtractionTransformer;
use Sanf\Core\Modules\Ocr\Dtos\ExtractOcrRequestDto;
use Sanf\Core\Modules\Ocr\Services\LlmOcrExtractionService;
use Spatie\Fractalistic\ArraySerializer;

class OcrExtractionController extends RestApiController
{
    public function extract(
        Guard $auth,
        string $xid,
        Request $request,
        LlmOcrExtractionService $extractionService
    ) {
        $inputs = $this->validate($request, [
            'mode' => ['required', 'string', 'in:single,multiple'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => [
                'required',
                'file',
                'mimetypes:application/pdf,image/jpeg,image/png,image/jpg',
                'max:100000',
            ],
            'llm_api_key' => ['nullable', 'string'],
        ]);

        $dto = clone new ExtractOcrRequestDto([
            'userId' => $auth->id(),
            'profileXid' => $xid,
            'mode' => $inputs['mode'],
            'files' => $request->file('files'),
            'llmApiKey' => $inputs['llm_api_key'] ?? null,
        ]);

        $result = $extractionService->execute($dto);

        return fractal($result, OcrExtractionTransformer::class)
            ->serializeWith(new ArraySerializer());
    }
}
