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

class LoginByGoogleService implements ApplicationServiceInterface
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
     * @throws \NbsPhp\Core\Exceptions\InvalidTokenException
     */
    public function execute($dto)
    {
        $jwtPayload = $this->jwt::verifyGoogleToken($dto->providerToken);
        $email = $jwtPayload['email'];
        $name = $jwtPayload['name'];
        $providerId = $jwtPayload['sub'];
        $isEmailVerified = $jwtPayload['email_verified'] ?? false;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new OAuthUserNotBoundException('invalid email format');
        }
        //TODO USING REPO
        return DB::transaction(function () use ($dto, $email, $name, $isEmailVerified, $providerId) {
            //MATCH WITH EXISTING USER BY SAME EMAIL
            //SKIP IF EMAIL STILL NOT VERIFIED
            $user = null;
            if ($isEmailVerified) {
                $user = $this->repository->newQuery()->where('username', $email)->first();
            }
            $userOAuth = UserOAuthModel::with('user')
                ->where([
                    'provider' => OAuthProvider::GOOGLE,
                    'provider_id' => $providerId,
                ])
                ->first();
            if (!$user && !$userOAuth) {
                throw new OAuthUserNotBoundException();
            }
            if ($user && !$userOAuth) {
                $userOAuth = UserOAuthModel::forceCreate([
                    'user_id' => $user->id,
                    'name' => $name,
                    'provider' => OAuthProvider::GOOGLE,
                    'provider_id' => $providerId,
                    'provider_token' => $dto->providerToken,
                ]);
            }

            $user = $userOAuth->user;
            $userOAuth->update([
                'name' => $name,
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
            $userSession = UserSessionModel::forceCreate([
                'auth_provider_id' => AuthProvider::GOOGLE,
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
