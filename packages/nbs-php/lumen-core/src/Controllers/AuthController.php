<?php


namespace NbsPhp\Core\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Enum\DevicePlatform;
use NbsPhp\Core\Services\AppLoginService;
use NbsPhp\Core\Services\RegisterService;

class AuthController extends RestController
{
    protected $middlewareOptions = [
        'except' => [
            'resetPassword',
            'submitResetPassword',
        ],
    ];

    public function loginApp(Request $request, AppLoginService $service)
    {
        $dto = (object)[
            'clientId' => $request->getUser(),
            'clientSecret' => $request->getPassword()
        ];
        $app = $service->execute($dto);

        return $this->responseOk()
            ->withHeaders([
                'X-Access-Token' => $app->accessToken,
                'X-Access-Expired-At' => $app->accessExpiredAt,
            ]);
    }

    private function validateRegister(Request $request)
    {
        $validated = $this->validate($request, [
            'full_name' => ['required', 'string',],
            'email' => ['required', 'email',],
            'password' => ['required', 'min:8',],
            'landline_number' => ['string', 'nullable', 'min:10',],
            'phone_number' => ['required', 'min:10',],
            'device' => ['required',],
            'device.device_id' => ['required', 'string',],
            'device.device_platform_id' => ['required', 'integer',],
            'device.notification_token' => ['nullable', 'string',],
            'device.notification_channel' => ['nullable', 'integer',],
            'device.metadata' => ['nullable',],
            'device.metadata.manufacturer' => ['nullable', 'string',],
            'device.metadata.model' => ['nullable', 'string',],
            'device.metadata.user_agent' => ['nullable', 'string',],
        ]);
        if (in_array((int)$request->input('device.device_platform_id'), [DevicePlatform::ANDROID, DevicePlatform::IOS])) {
            $validated += $this->validate($request, [
                'device.notification_token' => ['required', 'string',],
                'device.notification_channel' => ['required', 'integer',],
                'device.metadata' => ['required',],
                'device.metadata.manufacturer' => ['required', 'string',],
                'device.metadata.model' => ['required', 'string',],
            ]);
        } else if ((int)$request->input('device.device_platform_id') === DevicePlatform::WEB) {
            $validated += $this->validate($request, [
                'device.metadata' => ['required',],
                'device.metadata.user_agent' => ['required', 'string',],
            ]);
        }

        return $validated;
    }

    public function register(Request $request, RegisterService $service)
    {
        $input = $this->validateRegister($request);
        //TODO DTO
        $dto = (object)[
            'fullName' => $input['full_name'],
            'email' => $input['email'],
            'landlineNumber' => $input['landline_number'] ?? null,
            'phoneNumber' => $input['phone_number'],
            'password' => $input['password'],
        ];

        $service->execute($dto);

        return $this->responseOk();
    }

}
