<?php

namespace Sanf\Api\Modules\Ocr\Transformers;

use League\Fractal\TransformerAbstract;

class OcrExtractionTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'mode' => $dto->mode,
            'results' => $dto->results,
        ];
    }
}
