<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class BrowseESignDocumentTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'title' => $item->documentName ?? $item->documentId,
            'document_id' => $item->documentId,
            'status_id' => $item->statusId,
            'file_url' => ($item->documentFile) ? file_get_temp_url($item->documentFile->path) : null,
            'expired_at' => ($item->expiredAt) ? unix_timestamp($item->expiredAt) : null,
            'created_at' => ($item->createdAt) ? unix_timestamp($item->createdAt) : null,
        ];
    }
}
