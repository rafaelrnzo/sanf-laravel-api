<?php

namespace Sanf\Api\Modules\User\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\User\Services\AddPinService;
use Sanf\Core\Modules\User\Services\CheckPinService;

class AuthPinController extends RestApiController
{
    public function postAdd(
        Guard $auth,
        Request $request,
        AddPinService $service
    ) {
        $input = $this->validate($request, [
            'pin' => ['required', 'int', 'digits:6'],
        ]);

        $dto = (object)[
            'userId' => $auth->id(),
            'pin' => $input['pin'],
        ];

        $service->execute($dto);

        return $this->responseOk();
    }

    public function postCheck(
        Guard $auth,
        Request $request,
        CheckPinService $service
    ) {
        $input = $this->validate($request, [
            'pin' => ['required', 'int', 'digits:6'],
        ]);

        $dto = (object)[
            'userId' => $auth->id(),
            'pin' => $input['pin'],
        ];

        $service->execute($dto);

        return $this->responseOk();
    }


}
