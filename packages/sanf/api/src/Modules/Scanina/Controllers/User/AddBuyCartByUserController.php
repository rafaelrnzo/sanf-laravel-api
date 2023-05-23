<?php

namespace Sanf\Api\Modules\Scanina\Controllers\User;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Scanina\Services\GuzzleAddToCartBuyService;

class AddBuyCartByUserController extends RestApiController
{
    public function __invoke(
        string $xid,
        string $product_xid,
        Request $request,
        Guard $userAuth,
        GuzzleAddToCartBuyService $service
    ) {
        $addToCartRequestBody = (object)[
            'userId' => $userAuth->id(),
            'xid' => $xid,
            'productXid' => $product_xid,
        ];

        $result = $service->execute($addToCartRequestBody);

        return $this->responseOk();
    }
}
