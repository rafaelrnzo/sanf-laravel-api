<?php

namespace Sanf\Api\Modules\Financing\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Financing\Services\ValidateKtpService;
use Sanf\Core\Modules\Financing\Services\ValidateNpwpService;

class FinancingCompletionController extends RestApiController
{
    public function validateKtp($xid, ValidateKtpService $service)
    {
        $dto = (object)[
            'user_id' => $xid,
        ];

        $isValid = $service->execute($dto);

        return $this->responseOk('Success', [
            'is_valid' => $isValid,
        ]);
    }

    public function validateNpwp($xid, ValidateNpwpService $service)
    {
        $dto = (object)[
            'user_id' => $xid,
        ];

        $isValid = $service->execute($dto);

        return $this->responseOk('Success', [
            'is_valid' => $isValid,
        ]);
    }
}