<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class ESignDocumentOTPTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'request_xid' => $dto->transaction_no,
            'next_request_at' => unix_timestamp($dto->expired_at),
        ];
    }
}
