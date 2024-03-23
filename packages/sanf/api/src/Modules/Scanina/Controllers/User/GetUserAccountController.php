<?php

namespace Sanf\Api\Modules\Scanina\Controllers\User;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Scanina\Transformers\PostUserAccountResponseTransformer;
use Sanf\Core\Modules\Scanina\Services\GuzzleUserAccountService;
use Spatie\Fractalistic\ArraySerializer;

class GetUserAccountController extends RestApiController
{
    public function __invoke(string $xid, Request $request, Guard $userAuth, GuzzleUserAccountService $service)
    {
        $requestBodyDto = (object) [
            'userId' => $userAuth->id(),
            'xid' => $xid,
        ];
        $result = $service->execute($requestBodyDto);

        return fractal($result, PostUserAccountResponseTransformer::class)
            ->serializeWith(new ArraySerializer());
    }
}
