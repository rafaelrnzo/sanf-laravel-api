<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Payment\Transformers\PaymentStatsTransformer;
use Sanf\Core\Modules\Payment\UseCases\GetPaymentStatusCountUseCase;

final class PaymentController extends RestApiController
{
    public function stats(
        string $xid,
        Guard $auth,
        GetPaymentStatusCountUseCase $useCase
    )
    {
        $userId = $auth->id();

        $response = $useCase->execute($userId, $xid);

        return fractal($response)->transformWith(PaymentStatsTransformer::class);
    }
}
