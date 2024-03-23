<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ESignDocumentCompleteTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'document_id' => $item->document_id,
            'document_file_name' => $item->document_name,
            'document_owner_name' => '',
            'document_owner_email' => '',
            'download_url' => '',
            'stampers' => [],
            'signer' => fractal($item->signs, ESignDocumentCompleteSignTransformer::class)->serializeWith(ArraySerializer::class),
            'response_code' => $item->response_code,
        ];
    }
}
