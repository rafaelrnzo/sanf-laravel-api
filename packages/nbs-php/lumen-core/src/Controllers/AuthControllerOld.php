<?php

namespace NbsPhp\Core\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use NbsPhp\Core\Enum\AuthProvider;
use NbsPhp\Core\Exceptions\InvalidCredentialException;
use NbsPhp\Core\JWTHelper;
use NbsPhp\Core\Models\UserSessionModel;
use NbsPhp\Core\Repositories\ProfileRepositoryInterface;

class AuthControllerOld extends RestController
{
    protected $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        parent::__construct();
        $this->profileRepository = $profileRepository;
    }

    public function refresh(Request $request)
    {
        //TODO NOTIFICATION TOKEN
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        $jwtHelper = new JWTHelper();
        $jwtPayload = $jwtHelper->setToken($token)->getDecoded();
        $accessTokenExpiry = $jwtPayload->exp;
        $refreshToken = $jwtHelper->getRefreshToken();
        $refreshTokenExpiry = $jwtHelper->getRefreshTokenExpiration();

        $data = null;
        if (config('auth.token_on_body')) {
            $data = [
                'access_token' => $token,
                'access_token_expiry' => $accessTokenExpiry,
                'refresh_token' => $refreshToken,
                'refresh_token_expiry' => $refreshTokenExpiry,
            ];
        }
        return response()->json($data)->withHeaders([
            'x-access-token' => $token,
            'x-access-token-expiry' => $accessTokenExpiry,
            'x-refresh-token' => $refreshToken,
            'x-refresh-token-expiry' => $refreshTokenExpiry,
        ]);
    }

    public function login(Request $request)
    {
        $input = $this->validate($request, config('auth.rules.login'));
        $pipes = config('auth.services.login') ?? [];
        list($body, $header, $user) = app(Pipeline::class)
            ->send($input)
            ->through($pipes)
            ->then(function ($content) {
                return $this->doLogin($content);
            });

        return response()->json($body)->withHeaders($header);
    }

    public function doLogin($input)
    {
        if (!$token = Auth::attempt([
            'username' => $input['username'],
            'password' => $input['password']
        ])) {
            throw new InvalidCredentialException();
        }

        $jwtHelper = new JWTHelper();
        $jwtPayload = $jwtHelper->setToken($token)->getDecoded();
        $accessTokenExpiry = $jwtPayload->exp;
        $refreshToken = $jwtHelper->getRefreshToken();
        $refreshTokenExpiry = $jwtHelper->getRefreshTokenExpiration();

        $user = Auth::user();
        $profile = (array)$this->profileRepository->find($user->id);

        $data = [];
        if (config('auth.token_on_body')) {
            $data = [
                'access_token' => $token,
                'access_token_expiry' => $accessTokenExpiry,
                'refresh_token' => $refreshToken,
                'refresh_token_expiry' => $refreshTokenExpiry,
            ];
        }
        $transformer = config('auth.login_transformer');
        $profileResponse = fractal((object)$profile, new $transformer())->toArray();
        $body = array_merge($profileResponse, $data);
        $headers = [
            'x-access-token' => $token,
            'x-access-token-expiry' => $accessTokenExpiry,
            'x-refresh-token' => $refreshToken,
            'x-refresh-token-expiry' => $refreshTokenExpiry,
        ];

        //TODO LIMIT SESSION COUNT
        //TODO REPOSITORY SESSION
        UserSessionModel::query()->forceCreate([
            'user_id' => $user->id,
            'auth_provider_id' => AuthProvider::APP,
            'device_id' => $input['device_id'],
            'device_manufacturer' => $input['device_manufacturer'],
            'device_model' => $input['device_model'],
            'device_platform_id' => $input['device']['device_platform_id'],
            'signature' => $jwtPayload->jti,
            'expired_at' => $accessTokenExpiry,
        ]);

        return [$body, $headers, $user];
    }

    public function logout()
    {
        try {
            Auth::logout();
            return ['logout' => true];
        } catch (Exception $exception) {
            return ['logout' => false];
        }
    }

    public function changePassword(Request $request)
    {
        $this->validate($request,
            config('auth.input_validations.change_password.rules'),
            config('auth.input_validations.change_password.messages')
        );

        $hashedPassword = bcrypt($request->password);
        Auth::user()->update(['password' => $hashedPassword]);

        return $this->responseOk();
    }
}
