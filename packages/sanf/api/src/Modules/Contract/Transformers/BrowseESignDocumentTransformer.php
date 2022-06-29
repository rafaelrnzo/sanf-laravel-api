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
            'title' => $item->title,
            'status_id' => $item->status_id,
            'file_url' => $item->file_url,
            'expired_at' => unix_timestamp($item->expired_at),
            'created_at' => unix_timestamp($item->created_at),
        ];
    }
}