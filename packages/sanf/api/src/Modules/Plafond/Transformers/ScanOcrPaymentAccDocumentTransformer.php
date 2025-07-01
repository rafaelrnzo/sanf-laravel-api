<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use Sanf\Api\Modules\Asset\PrivateAssetFileSimpleTransformer;

final class ScanOcrPaymentAccDocumentTransformer extends PrivateAssetFileSimpleTransformer
{
    public function transform($dto)
    {
        $data = parent::transform($dto);

        return $data += [
            'document_no' => $dto->documentNo,
            'document_date' => $dto->documentDate,
        ];
    }
}
