<?php

namespace Sanf\Api\Modules\Financing\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Financing\Services\ValidateKtpService;
use Sanf\Core\Modules\Financing\Services\ValidateNpwpService;

class FinancingCompletionController extends RestApiController
{
    public function validateKtp($user_id, ValidateKtpService $service)
    {
        $dto = (object)[
            'user_id' => $user_id,
        ];

        $isValid = $service->execute($dto);

        return $this->responseOk('Success', [
            'is_valid' => $isValid,
        ]);
    }

    public function validateNpwp($user_id, ValidateNpwpService $service)
    {
        $dto = (object)[
            'user_id' => $user_id,
        ];

        $isValid = $service->execute($dto);

        return $this->responseOk('Success', [
            'is_valid' => $isValid,
        ]);
    }
}