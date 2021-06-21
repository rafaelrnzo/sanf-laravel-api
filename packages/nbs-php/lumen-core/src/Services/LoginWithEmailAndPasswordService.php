<?php


namespace NbsPhp\Core\Services;


use Illuminate\Support\Facades\Auth;
use NbsPhp\Core\Enum\AuthProvider;
use NbsPhp\Core\Exceptions\InvalidCredentialException;
use NbsPhp\Core\JWTHelper;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Models\UserSessionModel;

class LoginWithEmailAndPasswordService implements ApplicationServiceInterface
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
        if (!$token = Auth::attempt([
            'username' => $dto->username,
            'password' => $dto->password
        ])) {
            throw new InvalidCredentialException();
        }

        // TODO: check status user

        /** @var AuthModel $user */
        $user = Auth::user();

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
    }
}
