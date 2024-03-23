<?php

namespace Sanf\Api\Modules\Scanina\Controllers\User;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Core\Modules\Scanina\Services\GuzzleAddToCartServicesService;

class AddServiceCartByUserController extends RestApiController
{
    public function __invoke(
        string $xid,
        string $product_xid,
        Request $request,
        Guard $userAuth,
        GuzzleAddToCartServicesService $service,
        TransactionalSessionInterface $transactionalSession
    ) {
        $input = $this->validate($request, [
            'service_at' => 'required|integer',
            'notes' => 'nullable|string|max:255',
        ]);

        $addToCartRequestBody = (object) [
            'userId' => $userAuth->id(),
            'xid' => $xid,
            'servicedAt' => $input['service_at'],
            'notes' => $input['notes'] ?? null,
            'productXid' => $product_xid,
        ];

        $appService = new TransactionalApplicationService($service, $transactionalSession);
        $appService->execute($addToCartRequestBody);

        return $this->responseOk();
    }
}
