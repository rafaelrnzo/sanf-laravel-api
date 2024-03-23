<?php

namespace Sanf\Api\Modules\Scanina\Controllers\User;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Core\Modules\Scanina\Services\GuzzleAddToCartBuyService;

class AddBuyCartByUserController extends RestApiController
{
    public function __invoke(
        string $xid,
        string $product_xid,
        Request $request,
        Guard $userAuth,
        GuzzleAddToCartBuyService $service,
        TransactionalSessionInterface $transactionalSession
    ) {
        $addToCartRequestBody = (object) [
            'userId' => $userAuth->id(),
            'xid' => $xid,
            'productXid' => $product_xid,
        ];

        $appService = new TransactionalApplicationService($service, $transactionalSession);
        $appService->execute($addToCartRequestBody);

        return $this->responseOk();
    }
}
