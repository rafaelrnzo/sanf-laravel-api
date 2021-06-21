<?php


namespace NbsPhp\Core\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Dto\DeviceInfoRequestDto;
use NbsPhp\Core\Dto\SocialLoginDto;
use NbsPhp\Core\Enum\DevicePlatform;
use NbsPhp\Core\JWTHelper;
use NbsPhp\Core\Services\LoginByAppleService;
use NbsPhp\Core\Services\LoginByGoogleService;

class OAuthController extends RestController
{
    protected $middlewareOptions = [
        'except' => [
            'resetPassword',
            'submitResetPassword',
        ],
    ];

    protected $jwt;

    /**
     * AuthController constructor.
     * @param $jwt
     */
    public function __construct(JWTHelper $jwt)
    {
        parent::__construct();
        $this->jwt = $jwt;
    }

    protected function validateLogin(Request $request): array
    {
        $validated = $this->validate($request, [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'min:10', 'max:255'],
            'auth_token' => ['required', 'string', 'max:255'],
            'user_ref_id' => ['required', 'string', 'max:255'],
        ]);

        $validated += $this->validateDeviceInformation($request, 'device.');

        return $validated;
    }

    protected function validateDeviceInformation(Request $request, $prefix = null): array
    {
        $validated = $this->validate($request, [
            $prefix . "device_id" => ['required', 'string',],
            $prefix . "device_platform_id" => ['required', 'integer',],
            $prefix . "notification_token" => ['nullable', 'string',],
            $prefix . "notification_channel_id" => ['nullable', 'integer',],
            $prefix . "metadata" => ['nullable',],
            $prefix . "metadata.manufacturer" => ['nullable', 'string',],
            $prefix . "metadata.model" => ['nullable', 'string',],
            $prefix . "metadata.user_agent" => ['nullable', 'string',],
        ]);

        if (in_array((int)$request->input('device.device_platform_id'), [DevicePlatform::ANDROID, DevicePlatform::IOS])) {
            $validated += $this->validate($request, [
                $prefix . 'notification_token' => ['required', 'string',],
                $prefix . 'notification_channel_id' => ['required', 'integer',],
                $prefix . 'metadata' => ['required',],
                $prefix . 'metadata.manufacturer' => ['required', 'string',],
                $prefix . 'metadata.model' => ['required', 'string',],
            ]);
        } else if ((int)$request->input('device.device_platform_id') === DevicePlatform::WEB) {
            $validated += $this->validate($request, [
                $prefix . 'metadata' => ['required',],
                $prefix . 'metadata.user_agent' => ['required', 'string',],
            ]);
        }

        return $validated;
    }

    protected function newSocialLoginDto($input)
    {
        return new SocialLoginDto([
            'fullName' => $input['full_name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'providerId' => $input['user_ref_id'],
            'providerToken' => $input['auth_token'],
            'device' => new DeviceInfoRequestDto([
                'deviceId' => $input['device']['device_id'],
                'devicePlatformId' => $input['device']['device_platform_id'],
                'notificationToken' => $input['device']['notification_token'],
                'notificationChannelId' => $input['device']['notification_channel_id'],
                'metadata' => $input['device']['metadata']
            ])
        ]);
    }

    public function loginGoogle(Request $request, LoginByGoogleService $service)
    {
        $input = $this->validateLogin($request);

        $dto = $this->newSocialLoginDto($input);

        $user = $service->execute($dto);

        return $this->responseOk(
            'Success',
            fractal($user, config('auth.login_transformer'))
        )->withHeaders([
            'X-Access-Token' => $user->accessToken,
            'X-Access-Expired-At' => $user->accessExpiredAt,
            'X-Refresh-Token' => $user->refreshToken,
            'X-Refresh-Expired-At' => $user->refreshExpiredAt,
        ]);
    }

    public function loginApple(Request $request, LoginByAppleService $service)
    {
        $input = $this->validateLogin($request);

        $dto = $this->newSocialLoginDto($input);

        $user = $service->execute($dto);

        return $this->responseOk(
            'Success',
            fractal($user, config('auth.login_transformer'))
        )->withHeaders([
            'X-Access-Token' => $user->accessToken,
            'X-Access-Expired-At' => $user->accessExpiredAt,
            'X-Refresh-Token' => $user->refreshToken,
            'X-Refresh-Expired-At' => $user->refreshExpiredAt,
        ]);
    }
}
