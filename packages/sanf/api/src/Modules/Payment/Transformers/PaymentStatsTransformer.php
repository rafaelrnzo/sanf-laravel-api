<?php

namespace Sanf\Api\Modules\Payment\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Payment\Responses\PaymentStatusCountResponse;

final class PaymentStatsTransformer extends TransformerAbstract
{
    public function transform(PaymentStatusCountResponse $data)
    {
        return [
            'status' => $data->status,
            'total' => $data->total,
        ];
    }
}
