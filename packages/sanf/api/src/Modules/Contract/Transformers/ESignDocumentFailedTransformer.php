<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ESignDocumentFailedTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'document_id' => $item->document_id,
            'response_code' => $item->response_code,
        ];
    }
}
