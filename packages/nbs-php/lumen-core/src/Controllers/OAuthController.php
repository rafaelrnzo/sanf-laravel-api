<?php


namespace NbsPhp\Core\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Dto\DeviceInfoRequestDto;
use NbsPhp\Core\Dto\SocialLoginRequestDto;
use NbsPhp\Core\Dto\SocialRegisterRequestDto;
use NbsPhp\Core\Enum\DevicePlatform;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Core\Services\LoginByAppleService;
use NbsPhp\Core\Services\LoginByGoogleService;
use NbsPhp\Core\Services\RegisterByAppleServiceInterface;
use NbsPhp\Core\Services\RegisterByGoogleServiceInterface;

class OAuthController extends RestApiController
{
    protected $middlewareOptions = [
        'except' => [
            'resetPassword',
            'submitResetPassword',
        ],
    ];

    protected function validateLogin(Request $request): array
    {
        $validated = $this->validate($request, [
            'auth_token' => ['required', 'string']
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
        return new SocialLoginRequestDto([
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
            fractal($user, config('auth.transformers.login'))
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
            fractal($user, config('auth.transformers.login'))
        )->withHeaders([
            'X-Access-Token' => $user->accessToken,
            'X-Access-Expired-At' => $user->accessExpiredAt,
            'X-Refresh-Token' => $user->refreshToken,
            'X-Refresh-Expired-At' => $user->refreshExpiredAt,
        ]);
    }

    protected function validateRegister(Request $request): array
    {
        $validated = $this->validate($request, [
            'auth_token' => ['required', 'string',],
            'full_name' => ['required', 'string',],
            'email' => ['required', 'email',],
        ]);

        $validated += $this->validateDeviceInformation($request, 'device.');

        return $validated;
    }


    protected function newSocialRegisterDto($input)
    {
        return new SocialRegisterRequestDto([
            'providerToken' => $input['auth_token'],
            'fullName' => $input['full_name'],
            'email' => $input['email'],
            'device' => new DeviceInfoRequestDto([
                'deviceId' => $input['device']['device_id'],
                'devicePlatformId' => $input['device']['device_platform_id'],
                'notificationToken' => $input['device']['notification_token'],
                'notificationChannelId' => $input['device']['notification_channel_id'],
                'metadata' => $input['device']['metadata']
            ])
        ]);
    }

    public function registerGoogle(Request $request, RegisterByGoogleServiceInterface $service)
    {
        return $this->registerSocialPlatform($request, $service);
    }

    public function registerApple(Request $request, RegisterByAppleServiceInterface $service)
    {
        return $this->registerSocialPlatform($request, $service);
    }

    protected function registerSocialPlatform($request, ApplicationServiceInterface $service)
    {
        $input = $this->validateRegister($request);
        $dto = $this->newSocialRegisterDto($input);
        $user = $service->execute($dto);

        if (is_null(optional($user)->token)) {
            return $this->responseOk(
                'Success',
                fractal($user, config('auth.transformers.login'))
            );
        }

        return $this->responseOk(
            'Success',
            fractal($user, config('auth.transformers.login'))
        )->withHeaders([
            'X-Access-Token' => $user->token->accessToken,
            'X-Access-Expired-At' => $user->token->accessExpiredAt,
            'X-Refresh-Token' => $user->token->refreshToken,
            'X-Refresh-Expired-At' => $user->token->refreshExpiredAt,
        ]);
    }
}
