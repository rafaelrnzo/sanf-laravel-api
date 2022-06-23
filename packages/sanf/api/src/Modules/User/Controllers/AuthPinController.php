<?php

namespace Sanf\Api\Modules\User\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\User\Transformers\RequestForgotPinTransformer;
use Sanf\Core\Modules\User\Services\AddPinService;
use Sanf\Core\Modules\User\Services\CheckPinService;
use Sanf\Core\Modules\User\Services\RequestForgotPinService;
use Sanf\Core\Modules\User\Services\ResetPinService;
use Sanf\Core\Modules\User\Services\UpdatePinService;

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

    public function postUpdate(
        Guard $auth,
        Request $request,
        UpdatePinService $service
    ) {
        $input = $this->validate($request, [
            'current_pin' => ['required', 'int', 'digits:6'],
            'new_pin' => ['required', 'int', 'digits:6'],
        ]);

        $dto = (object)[
            'userId' => $auth->id(),
            'current_pin' => $input['current_pin'],
            'new_pin' => $input['new_pin'],
        ];

        $service->execute($dto);

        return $this->responseOk();
    }

    public function postRequestForgot(
        Guard $auth,
        Request $request,
        RequestForgotPinService $service
    ) {
        $input = $this->validate($request, [
            'password' => ['required', 'min:8', 'regex:/^(?=.*\d)(?=.*[a-zA-Z])/'],
        ]);

        $dto = (object)[
            'userId' => $auth->id(),
            'password' => $input['password'],
        ];

        $result = $service->execute($dto);

        return fractal($result, new RequestForgotPinTransformer());
    }

    public function postReset(
        Guard $auth,
        Request $request,
        ResetPinService $service
    ) {
        $input = $this->validate($request, [
            'pin' => ['required', 'int', 'digits:6'],
            'code' => ['required', 'int', 'digits:4']
        ]);

        $dto = (object)[
            'userId' => $auth->id(),
            'pin' => $input['pin'],
            'code' => $input['code'],
        ];

        $service->execute($dto);

        return $this->responseOk();
    }

}
