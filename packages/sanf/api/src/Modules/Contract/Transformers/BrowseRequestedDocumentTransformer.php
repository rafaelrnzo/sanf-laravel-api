<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;

class BrowseRequestedDocumentTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'request_no' => $dto->request_no,
            'request_date' => $dto->request_date,
            'contract_no' => $dto->contract_no,
            'total_document' => $dto->total_document,
            'total_uploaded_document' => $dto->total_uploaded_document,
            'documents' => fractal($dto->documents, new ListItemRequestedDocumentTransformer())->serializeWith(
                new ArraySerializer()
            ),
        ];
    }
}