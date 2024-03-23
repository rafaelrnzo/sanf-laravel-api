<?php

namespace Sanf\Api\Modules\Scanina\Controllers\User;

use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Scanina\Services\GuzzleResendUserMailVerificationService;

class ResendUserMailVerificationController extends RestApiController
{
    public function __invoke(
        string $xid,
        Guard $userAuth,
        GuzzleResendUserMailVerificationService $service
    ) {
        $requestBodyDto = (object) [
            'userId' => $userAuth->id(),
            'xid' => $xid,
        ];

        $result = $service->execute($requestBodyDto);

        return $this->responseOk();
    }
}
