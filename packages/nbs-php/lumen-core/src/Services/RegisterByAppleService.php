<?php


namespace NbsPhp\Core\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Enum\AuthProvider;
use NbsPhp\Core\Enum\EntityType;
use NbsPhp\Core\Enum\OAuthProvider;
use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Exceptions\EmailAlreadyExistException;
use NbsPhp\Core\Exceptions\OAuthUserAlreadyBoundException;
use NbsPhp\Core\JWTHelper;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Models\UserOAuthModel;
use NbsPhp\Core\Models\UserSessionModel;

class RegisterByAppleService implements ApplicationServiceInterface
{
    protected $jwt;

    protected $repository;

    /**
     * RegisterService constructor.
     * @param $jwt
     */
    public function __construct(JWTHelper $jwt, AuthModel $repository) //TODO USE REPOSITORY
    {
        $this->jwt = $jwt;
        $this->repository = $repository;
    }

    public function execute($dto)
    {
        //TODO REPOSITORY
        $user = DB::transaction(function () use ($dto) {
            $userOAuth = UserOAuthModel::with('user')
                ->where([
                    'provider' => OAuthProvider::APPLE,
                    'provider_id' => $dto->providerId,
                ])
                ->first();

            if ($userOAuth) {
                throw new OAuthUserAlreadyBoundException();
            }

            /** @var AuthModel $user */
            $user = $this->repository->newQuery()->where('username', $dto->email)->first();
            if (is_null($user)) {
                $user = $this->repository->newQuery()->forceCreate([
                    'full_name' => $dto->fullName,
                    'username' => $dto->email,
                    'landline_number' => $dto->landlineNumber,
                    'phone_number' => $dto->phoneNumber,
                    'password' => bcrypt($dto->password),
                    'password_updated_at' => Carbon::now(),
                    'status_id' => UserStatus::INACTIVE,
                    'entity_type_id' => EntityType::ADMIN, //TODO CONFIGURABLE
                ]);
            }

            UserOAuthModel::forceCreate([
                'user_id' => $user->id,
                'name' => $dto->fullName,
                'provider' => OAuthProvider::GOOGLE,
                'provider_id' => $dto->providerId,
                'provider_token' => $dto->providerToken,
            ]);

            return $user;
        });

        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return null;
        }

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $token = Auth::login($user);

        $jwtToken = $this->jwt->setToken($token);
        $accessTokenExpiredAt = $jwtToken->getDecoded()->exp;
        $signature = $jwtToken->getDecoded()->jti;

        $device = $dto->device;

        //TODO REPOSITORY
        /** @var UserSessionModel $userSession */
        //TODO SESSION REPOSITORY
        $userSession = UserSessionModel::forceCreate([
            'auth_provider_id' => AuthProvider::APP,
            'user_id' => $user->id,
            'device_id' => $device->deviceId,
            'device_platform_id' => $device->devicePlatformId,
//            'notification_channel_id' => $device->notificationChannelId ?? null,
//            'notification_token' => $device->notificationToken ?? null,
            'device_metadata' => $metadata->metadata ?? null,
            'device_user_agent' => $metadata->userAgent ?? null,
            'signature' => $signature,
            'expired_at' => $accessTokenExpiredAt,
        ]);

        $refreshToken = $jwtToken->getRefreshToken($userSession->id);
        $refreshTokenExpiredAt = $jwtToken->getDecodedRefreshToken()->exp;

        //TODO DTO
        return json_decode(json_encode(array_merge($user->toArray(), [
            'accessToken' => $token,
            'accessExpiredAt' => $accessTokenExpiredAt,
            'refreshToken' => $refreshToken,
            'refreshExpiredAt' => $refreshTokenExpiredAt,
        ])));
    }
}
