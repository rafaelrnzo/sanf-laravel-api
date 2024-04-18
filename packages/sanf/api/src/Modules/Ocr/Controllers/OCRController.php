<?php

namespace Sanf\Api\Modules\Ocr\Controllers;

use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Ocr\Transformers\UserOCRPermissionTransformer;
use Sanf\Core\Modules\Ocr\Services\GetUserOCRPermissionService;
use Spatie\Fractalistic\ArraySerializer;

class OCRController extends RestApiController
{
    public function getUserPermission(Guard $auth, string $xid, GetUserOCRPermissionService $service)
    {
        $dto = (object) [
            'userId' => $auth->id(),
            'customerId' => $xid,
        ];

        $result = $service->execute($dto);

        return fractal($result, UserOCRPermissionTransformer::class)
            ->serializeWith(new ArraySerializer());
    }
}
