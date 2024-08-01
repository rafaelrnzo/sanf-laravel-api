<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Contract\Transformers\GetESignUserTransformer;
use Sanf\Core\Modules\Contract\Services\SanfESignUserService;
use Spatie\Fractalistic\ArraySerializer;

class ESignDocumentV2Controller extends RestApiController
{
    public function getUser(
        string $xid,
        Guard $auth,
        SanfESignUserService $eSignSanfUserService
    ) {
        $dto = (object) [
            'profileXid' => $xid,
            'userId' => $auth->id(),
        ];

        $eSignSanfUser = $eSignSanfUserService->execute($dto);

        return fractal($eSignSanfUser, GetESignUserTransformer::class)
            ->serializeWith(new ArraySerializer());
    }
}
