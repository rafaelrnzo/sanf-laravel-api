<?php

namespace Sanf\Api\Modules\RequestedDocument\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class BrowseRequestedDocumentTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'request_no' => $dto->request_no,
            'request_date' => ($dto->request_at)->format('d F Y'),
            'document_no' => $dto->document_no,
            'total_document' => $dto->total_document,
            'total_uploaded_document' => $dto->total_uploaded ?? 0,
            'documents' => fractal($dto->documents, new ListItemRequestedDocumentTransformer())->serializeWith(
                ArraySerializer::class
            ),
        ];
    }
}
