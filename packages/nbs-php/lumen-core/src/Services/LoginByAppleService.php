<?php


namespace NbsPhp\Core\Services;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Dto\SocialLoginRequestDto;
use NbsPhp\Core\Enum\AuthProvider;
use NbsPhp\Core\Enum\OAuthProvider;
use NbsPhp\Core\Exceptions\OAuthUserNotBoundException;
use NbsPhp\Core\JWTHelper;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Models\UserOAuthModel;
use NbsPhp\Core\Models\UserSessionModel;

class LoginByAppleService implements ApplicationServiceInterface
{
    protected $jwt;

    protected $repository;

    public function __construct(JWTHelper $jwt, AuthModel $repository) //TODO USE REPOSITORY
    {
        $this->jwt = $jwt;
        $this->repository = $repository;
    }

    /**
     * @param SocialLoginRequestDto $dto
     * @return mixed
     */
    public function execute($dto)
    {
        $this->jwt::verifyAppleIdToken($dto->providerToken);

        //TODO USING REPO
        return DB::transaction(function () use ($dto) {
            $userOAuth = UserOAuthModel::with('user')
                ->where([
                    'provider' => OAuthProvider::APPLE,
                    'provider_id' => $dto->providerId,
                ])
                ->first();

            if (!$userOAuth) {
                throw new OAuthUserNotBoundException();
            }

            $user = $userOAuth->user;

            $userOAuth->update([
                'name' => $dto->fullName,
                'provider_token' => $dto->providerToken,
            ]);

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $token = Auth::login($user);

            $jwtToken = $this->jwt->setToken($token);
            $accessTokenExpiredAt = $jwtToken->getDecoded()->exp;
            $signature = $jwtToken->getDecoded()->jti;

            $device = $dto->device;

            //TODO REPOSITORY
            /** @var UserSessionModel $userSession */
            //TODO SESSION REPOSITORY
            $userSession =  UserSessionModel::forceCreate([
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
        });
    }
}
