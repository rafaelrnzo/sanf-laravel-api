<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Api\Modules\Asset\PrivateAssetFileSimpleTransformer;
use Spatie\Fractalistic\ArraySerializer;

final class InvoicePlafondUploadDocumentTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'document' => [
                'url' => file_get_temp_url($dto->document->path ?? null),
                'file_name' => $dto->document->fileName,
                'origin_name' => $dto->document->originName ?? $dto->document->fileName,
            ],
            'photos' => fractal($dto->photos)
                ->transformWith(PrivateAssetFileSimpleTransformer::class)
                ->serializeWith(new ArraySerializer()),
        ];
    }
}
