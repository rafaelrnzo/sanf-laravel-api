<?php


namespace NbsPhp\Core\Controllers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use NbsPhp\Core\Dto\AppLoginRequestDto;
use NbsPhp\Core\Dto\DeviceInfoRequestDto;
use NbsPhp\Core\Dto\LoginRequestDto;
use NbsPhp\Core\Dto\RegisterRequestDto;
use NbsPhp\Core\Dto\UpdateSessionRequestDto;
use NbsPhp\Core\Enum\DevicePlatform;
use NbsPhp\Core\Exceptions\ApiException;
use NbsPhp\Core\Exceptions\UnauthorizedException;
use NbsPhp\Core\Exceptions\UserActivationFailedException;
use NbsPhp\Core\Exceptions\UserAlreadyActivatedException;
use NbsPhp\Core\Exceptions\VerifyEmailFailedException;
use NbsPhp\Core\Jwt\JWTHelper;
use NbsPhp\Core\Services\ActivateUserServiceInterface;
use NbsPhp\Core\Services\AppLoginService;
use NbsPhp\Core\Services\ChangePasswordService;
use NbsPhp\Core\Services\LoginWithEmailAndPasswordService;
use NbsPhp\Core\Services\LogoutService;
use NbsPhp\Core\Services\RegisterByEmailServiceInterface;
use NbsPhp\Core\Services\SendEmailActivationService;
use NbsPhp\Core\Services\SendEmailVerificationService;
use NbsPhp\Core\Services\UpdateSessionService;
use NbsPhp\Core\Services\ValidateUserActivatedService;
use NbsPhp\Core\Services\VerifyEmailServiceInterface;

class AuthController extends RestApiController
{
    protected $middlewareOptions = [
        'except' => [
            'resetPassword',
            'submitResetPassword',
        ],
    ];

    public function loginApp(Request $request, AppLoginService $service)
    {
        $dto = new AppLoginRequestDto([
            'clientId' => $request->getUser(),
            'clientSecret' => $request->getPassword()
        ]);
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

        $devicePlatformId = (int)$request->input('device.device_platform_id');
        if (in_array($devicePlatformId, [DevicePlatform::ANDROID, DevicePlatform::IOS])) {
            $validated += $this->validate($request, [
                $prefix . 'notification_token' => ['required', 'string',],
                $prefix . 'notification_channel_id' => ['required', 'integer',],
                $prefix . 'metadata' => ['required',],
                $prefix . 'metadata.manufacturer' => ['required', 'string',],
                $prefix . 'metadata.model' => ['required', 'string',],
            ]);
        } else if ($devicePlatformId === DevicePlatform::WEB) {
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
            'password' => config('auth.input_validations.password.rule', ['required']),
            'landline_number' => ['string', 'nullable', 'min:10',],
            'phone_number' => ['required', 'min:10',],
        ], config('auth.input_validations.password.messages'));

        $validated += $this->validateDeviceInformation($request, 'device.');

        return $validated;
    }

    public function register(Request $request, RegisterByEmailServiceInterface $service)
    {
        $input = $this->validateRegister($request);

        $dto = new RegisterRequestDto([
            'fullName' => $input['full_name'],
            'email' => $input['email'],
            'landlineNumber' => $input['landline_number'] ?? null,
            'phoneNumber' => $input['phone_number'],
            'password' => $input['password'],
        ]);

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
        $dto = new LoginRequestDto([
            'username' => $input['username'],
            'password' => $input['password'],
            'device' => new DeviceInfoRequestDto([
                'deviceId' => $input['device']['device_id'],
                'devicePlatformId' => $input['device']['device_platform_id'],
                'notificationToken' => $input['device']['notification_token'],
                'notificationChannelId' => $input['device']['notification_channel_id'],
                'metadata' => $input['device']['metadata']
            ])
        ]);
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

    public function logout(Request $request, LogoutService $service)
    {
        $result = $service->execute(null);
        return fractal($result, config('auth.transformers.logout'));
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
            'device' => new DeviceInfoRequestDto([
                'deviceId' => $input['device_id'],
                'devicePlatformId' => $input['device_platform_id'] ?? null,
                'notificationToken' => $input['notification_token'] ?? null,
                'notificationChannelId' => $input['notification_channel_id'],
                'metadata' => $input['metadata']
            ])
        ]);

        $user = $service->execute($dto);

        return fractal($user, config('auth.transformers.login'))->respond(200, [
            'X-Access-Token' => $user->accessToken,
            'X-Access-Expired-At' => $user->accessExpiredAt,
            'X-Refresh-Token' => $user->refreshToken,
            'X-Refresh-Expired-At' => $user->refreshExpiredAt,
        ]);
    }

    public function verifyEmailPage(Request $request, VerifyEmailServiceInterface $service)
    {
        try {
            $jwt = $this->extractVerifyEmailToken($request);
            $dto = (object)[
                'userId' => $jwt->sub,
                'token' => $jwt->token
            ];
            $service->execute($dto);
            $message = __('Email berhasil diaktivasi');
        } catch (\Exception $e) {
            report($e);
            $message = $e->getMessage();
            if ($e instanceof ClientException || $e instanceof ServerException) {
                $message = __('Terjadi Kesalahan, Harap Hubungi Administrator');
            }
            return view('core::layouts.message', ['message' => $message]);
        }

        return view(config('auth.views.verify-email'), ['message' => $message]);
    }

    public function verifyEmailByApp(Request $request, VerifyEmailServiceInterface $service)
    {
        $jwt = $this->extractVerifyEmailToken($request);
        $dto = (object)[
            'userId' => $jwt->sub,
            'token' => $jwt->token
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    protected function extractVerifyEmailToken(Request $request)
    {
        //TODO COONFIGURABLE HEADER SOURCE NAME
        $jwtToken = $request->token ?? str_replace('Bearer ', '', $request->header('X-Email-Verification-Token'));
        $decodedToken = (new JWTHelper())->setToken($jwtToken)->getDecoded();
        if (is_null($decodedToken)) {
            throw new VerifyEmailFailedException('Verify Token Invalid');
        }
        return $decodedToken;
    }

    public function requestEmailVerification(Request $request, SendEmailVerificationService $service)
    {
        $this->validate($request, [
            'email' => ['required', 'email']
        ]);
        $dto = (object)[
            'email' => $request->input('email'),
        ];
        try {
            $service->execute($dto);
        } catch (VerifyEmailFailedException $exception) {
            // ignore error if email not found
        }
        return $this->responseOk();
    }

    public function requestActivation(Request $request, SendEmailActivationService $service)
    {
        $this->validate($request, [
            'email' => ['required', 'email']
        ]);
        $dto = (object)[
            'email' => $request->input('email'),
        ];
        try {
            $service->execute($dto);
        } catch (UserActivationFailedException $exception) {
            // ignore error if email not found
        }
        return $this->responseOk();
    }

    public function userActivationPage(Request $request, ValidateUserActivatedService $service)
    {
        try {
            $jwt = $this->extractActivationToken($request);
            $dto = (object)[
                'userId' => $jwt->sub,
                'token' => $jwt->token,
            ];
            $user = $service->execute($dto);
            return view(config('auth.views.user-activation'))->with([
                    'token' => $request->token,
                    'email' => $user->username,
                    'error' => $request->session()->get('error'),
                ]
            );
        } catch (UserAlreadyActivatedException $exception) {
            return view(config('auth.views.password-set'));
        } catch (ApiException $exception) {
            report($exception);
            if ($request->expectsJson()) {
                throw $exception;
            }
            return view(config('auth.views.user-activation'))->with(
                ['token' => $request->token, 'error' => $exception->getMessage()]
            )->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function userActivationByWeb(Request $request, ActivateUserServiceInterface $service)
    {
        try {
            if ($request->has('password_confirmation')) {
                $this->validate($request, ['password' => 'confirmed']);
            }
            $input = $this->validate($request, [
                'password' => config('auth.input_validations.password.rule', ['required'])
            ], config('auth.input_validations.password.messages'));
            $jwt = $this->extractActivationToken($request);
            $dto = (object)[
                'userId' => $jwt->sub,
                'token' => $jwt->token,
                'password' => $input['password']
            ];
            $service->execute($dto);
        } catch (ValidationException $exception) {
            return redirect_with_session()
                ->route(extract_route_name($request), ['token' => $request->token])
                ->with(['error' => extract_validation_message($exception)]);
        } catch (ApiException $exception) {
            return redirect_with_session()
                ->route(extract_route_name($request), ['token' => $request->token])
                ->with(['error' => $exception->getMessage()]);
        }
        return redirect()->route('user.activate-page', ['token' => $request->token]);
    }

    public function userActivationByApp(Request $request, ActivateUserServiceInterface $service)
    {
        $input = $this->validate($request, [
            'password' => config('auth.input_validations.password.rule', ['required'])
        ], config('auth.input_validations.password.messages'));
        $jwt = $this->extractActivationToken($request);
        $dto = (object)[
            'userId' => $jwt->sub,
            'token' => $jwt->token,
            'password' => $input['password']
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    protected function extractActivationToken(Request $request)
    {
        //TODO COONFIGURABLE HEADER SOURCE NAME
        $jwtToken = $request->token ?? str_replace('Bearer ', '', $request->header('X-Activation-Token'));
        $decodedToken = (new JWTHelper())->setToken($jwtToken)->getDecoded();
        if (is_null($decodedToken)) {
            throw new UserActivationFailedException('Activation Token Invalid, Please Request Again');
        }
        return $decodedToken;
    }

    public function changePassword(Request $request, Guard $auth, ChangePasswordService $service)
    {
        $input = $this->validate($request, [
            'current_password' => ['required', 'string'],
            'new_password' => config('auth.input_validations.password.rule', ['required']),
        ]);

        $dto = (object)[
            'userId' => $auth->id(),
            'currentPassword' => $input['current_password'],
            'newPassword' => $input['new_password'],
        ];
        $service->execute($dto);
        return $this->responseOk();
    }
}
