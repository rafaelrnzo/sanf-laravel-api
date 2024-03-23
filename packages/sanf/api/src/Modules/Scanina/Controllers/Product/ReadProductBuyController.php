<?php

namespace Sanf\Api\Modules\Scanina\Controllers\Product;

use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Scanina\Transformers\ReadProductBuyResponseTransformer;
use Sanf\Core\Modules\Scanina\Services\GuzzleReadProductBuyService;

class ReadProductBuyController extends RestApiController
{
    public function __invoke(string $xid, Guard $userAuth, GuzzleReadProductBuyService $service)
    {
        $dto = (object) [
            'xid' => $xid,
            'userId' => $userAuth->id(),
        ];
        $result = $service->execute($dto);

        return fractal($result, ReadProductBuyResponseTransformer::class);
    }
}
