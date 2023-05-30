<?php

namespace Sanf\Api\Modules\Scanina\Controllers\User;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Core\Modules\Scanina\Services\GuzzleAddToCartRentService;

class AddRentCartByUserController extends RestApiController
{
    public function __invoke(
        string $xid,
        string $product_xid,
        Request $request,
        Guard $userAuth,
        GuzzleAddToCartRentService $service,
        TransactionalSessionInterface $transactionalSession
    ) {
        $input = $this->validate($request, [
            'start_at' => 'required|integer',
            'end_at' => 'required|integer|gte:start_at',
        ]);

        $addToCartRequestBody = (object)[
            'userId' => $userAuth->id(),
            'xid' => $xid,
            'startedAt' => $input['start_at'],
            'endedAt' => $input['end_at'],
            'productXid' => $product_xid,
        ];

        $appService = new TransactionalApplicationService($service, $transactionalSession);
        $appService->execute($addToCartRequestBody);

        return $this->responseOk();
    }
}
