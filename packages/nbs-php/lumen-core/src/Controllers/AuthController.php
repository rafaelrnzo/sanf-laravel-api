<?php


namespace NbsPhp\Core\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NbsPhp\Core\Dto\DeviceInfoRequestDto;
use NbsPhp\Core\Dto\UpdateSessionRequestDto;
use NbsPhp\Core\Enum\DevicePlatform;
use NbsPhp\Core\Exceptions\UnauthorizedException;
use NbsPhp\Core\JWTHelper;
use NbsPhp\Core\Services\AppLoginService;
use NbsPhp\Core\Services\LoginWithEmailAndPasswordService;
use NbsPhp\Core\Services\LogoutService;
use NbsPhp\Core\Services\RegisterService;
use NbsPhp\Core\Services\UpdateSessionService;

class AuthController extends RestController
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

    protected function validateRegister(Request $request): array
    {
        $validated = $this->validate($request, [
            'full_name' => ['required', 'string',],
            'email' => ['required', 'email',],
            'password' => ['required', 'min:8',],
            'landline_number' => ['string', 'nullable', 'min:10',],
            'phone_number' => ['required', 'min:10',],
        ]);

        $validated += $this->validateDeviceInformation($request, 'device.');

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

    protected function validateLogin(Request $request): array
    {
        $validated = $this->validate($request, [
            'username' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string']
        ]);

        $validated += $this->validateDeviceInformation($request, 'device.');

        return $validated;
    }

    public function login(Request $request, LoginWithEmailAndPasswordService $service)
    {
        $input = $this->validateLogin($request);
        $dto = (object)[
            'username' => $input['username'],
            'password' => $input['password'],
            'device' => new DeviceInfoRequestDto([
                'deviceId' => $input['device']['device_id'],
                'devicePlatformId' => $input['device']['device_platform_id'],
                'notificationToken' => $input['device']['notification_token'],
                'notificationChannelId' => $input['device']['notification_channel_id'],
                'metadata' => $input['device']['metadata']
            ])
        ];
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

    public function logout(Request $request, LogoutService $service)
    {
        $result = $service->execute(null);
        return fractal($result, config('auth.logout_transformer'));
    }

    protected function validateBearerToken(Request $request)
    {
        if (!$request->headers->has('Authorization')) {
            throw new UnauthorizedException();
        }

        [$refreshToken] = sscanf($request->headers->get('Authorization'), 'Bearer %s');

        if (is_null($refreshToken)) {
            throw new UnauthorizedException();
        }

        return $refreshToken;
    }

    /**
     * @param Request $request
     * @param UpdateSessionService $service
     * @return \Illuminate\Http\JsonResponse
     * @throws UnauthorizedException
     */
    public function refreshToken(Request $request, UpdateSessionService $service)
    {
        $refreshToken = $this->validateBearerToken($request);

        $input = $this->validateDeviceInformation($request);

        $dto = new UpdateSessionRequestDto([
            'refreshToken' => $refreshToken,
            'device' => [
                'deviceId' => $input['device_id'],
                'devicePlatformId' => $input['device_platform_id'] ?? null,
                'notificationToken' => $input['notification_token'] ?? null,
                'notificationChannelId' => $input['notification_channel_id'],
                'metadata' => $input['metadata']
            ]]);

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
