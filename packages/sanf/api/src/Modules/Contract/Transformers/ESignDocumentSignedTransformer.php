<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ESignDocumentSignedTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'document_id' => $item->document_id,
            'signer_email' => $item->email,
            'stamp' => [],
            'sign' => fractal($item->signs, ESignDocumentSignedSignTransformer::class)->serializeWith(ArraySerializer::class),
            'response_code' => $item->response_code,
        ];
    }
}
