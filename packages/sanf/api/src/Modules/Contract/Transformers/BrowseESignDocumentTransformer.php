<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseESignDocumentTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        $documentName = $item->documentName ?? $item->documentId;

        return [
            'xid' => $item->xid,
            'title' => preg_replace('/^final-/', '', $documentName),
            'document_id' => $item->documentId,
            'reference_no' => $item->referenceNo,
            'status_id' => $item->statusId,
            'expired_at' => ($item->expiredAt) ? unix_timestamp($item->expiredAt) : null,
            'created_at' => ($item->createdAt) ? unix_timestamp($item->createdAt) : null,
        ];
    }
}
