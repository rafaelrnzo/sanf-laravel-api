<?php

namespace Sanf\Api\Modules\Scanina\Controllers\User;

use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Scanina\Transformers\ReadProductServiceResponseTransformer;
use Sanf\Core\Modules\Scanina\Services\ReadServiceCartByUserService;

class ReadServiceCartByUserController extends RestApiController
{
    public function __invoke(
        string $xid,
        string $product_xid,
        Guard $userAuth,
        ReadServiceCartByUserService $service
    ) {
        $dto = (object)[
            'xid' => $xid,
            'productXid' => $product_xid,
            'userId' => $userAuth->id(),
        ];
        $result = $service->execute($dto);

        return fractal($result, ReadProductServiceResponseTransformer::class);
    }
}
