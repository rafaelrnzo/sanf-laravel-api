<?php

namespace Sanf\Api\Modules\Scanina\Controllers\Product;

use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Scanina\Transformers\ReadProductSparePartResponseTransformer;
use Sanf\Core\Modules\Scanina\Services\GuzzleReadProductSparePartService;

class ReadProductSparePartController extends RestApiController
{
    public function __invoke(string $xid, Guard $userAuth, GuzzleReadProductSparePartService $service)
    {
        $dto = (object)[
            'xid' => $xid,
            'userId' => $userAuth->id(),
        ];
        $result = $service->execute($dto);

        return fractal($result, ReadProductSparePartResponseTransformer::class);
    }
}
