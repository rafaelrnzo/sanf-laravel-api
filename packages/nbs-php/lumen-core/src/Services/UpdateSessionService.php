<?php

namespace NbsPhp\Core\Services;

use Illuminate\Support\Facades\Auth;
use NbsPhp\Core\Enum\AuthProvider;
use NbsPhp\Core\Exceptions\InvalidRefreshTokenException;
use NbsPhp\Core\Jwt\JWTHelper;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Models\UserSessionModel;

class UpdateSessionService implements ApplicationServiceInterface
{
    protected $jwt;

    protected $repository;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(JWTHelper $jwt, AuthModel $repository)
    {
        $this->jwt = $jwt;
        $this->repository = $repository;
    }

    /**
     * @param null $dto
     * @return mixed
     */
    public function execute($dto = null)
    {
        $jwtRefreshToken = $this->jwt->setToken($dto->refreshToken);

        if (empty($sessionId = optional($jwtRefreshToken->getDecoded())->sub)) {
            throw new InvalidRefreshTokenException();
        }

        $device = $dto->device;

        //TODO SESSION REPOSITORY
        $session = UserSessionModel::query()->findOrFail($sessionId);
        $user = $this->repository->find($session->user_id);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $token = Auth::login($user);

        $jwtToken = $this->jwt->setToken($token);
        $accessTokenExpiredAt = $jwtToken->getDecoded()->exp;
        $signature = $jwtToken->getDecoded()->jti;

        $session->forceFill([
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
        ])->save();

        $refreshToken = $jwtToken->getRefreshToken($session->id);
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
