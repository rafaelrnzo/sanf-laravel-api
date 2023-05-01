<?php

namespace Sanf\Api\Modules\RequestedDocument\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseHistoryRequestedDocumentTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'upload_at' => $dto->upload_at,
            'filename' => $dto->filename,
        ];
    }
}
