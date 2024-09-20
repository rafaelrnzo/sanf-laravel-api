<?php

namespace NbsPhp\Core\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use NbsPhp\Core\Enum\AuthProvider;
use NbsPhp\Core\Enum\EntityType;
use NbsPhp\Core\Enum\OAuthProvider;
use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Exceptions\OAuthEmailRequiredException;
use NbsPhp\Core\Exceptions\OAuthUserAlreadyBoundException;
use NbsPhp\Core\Jwt\JWTHelper;
use NbsPhp\Core\Models\UserSessionModel;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\AuthEncryptedModel;
use Sanf\Core\Modules\User\Repositories\UserOAuthRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class RegisterByAppleService implements RegisterByAppleServiceInterface
{
    protected $jwt;

    protected $userRepository;
    protected $userOAuthRepository;

    public function __construct(
        JWTHelper $jwt,
        UserRepositoryInterface $userRepository,
        UserOAuthRepositoryInterface $userOAuthRepository
    )
    {
        $this->jwt = $jwt;
        $this->userRepository = $userRepository;
        $this->userOAuthRepository = $userOAuthRepository;
    }

    public function execute($dto = null)
    {
        $jwtPayload = $this->jwt::verifyAppleIdToken($dto->providerToken);
        $providerId = $jwtPayload['sub'];
        $isEmailVerified = $jwtPayload['email_verified'] ?? false;
        $isPrivateEmail = $jwtPayload['is_private_email'] ?? false;
        $email = $isPrivateEmail ? $dto->email : $jwtPayload['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new OAuthEmailRequiredException('invalid email format');
        }

        $sodiumQuery = SodiumEncryption::query();

        $sodiumQuery->multipleBeginTransaction();

        try {
            $sodiumQuery->hideLogStatement();

            $userOAuth = $this->userOAuthRepository->findByProvider(OAuthProvider::APPLE, $providerId);

            if ($userOAuth) {
                throw new OAuthUserAlreadyBoundException();
            }

            /** @var AuthEncryptedModel $user */
            $user = $this->userRepository->findByEmail($email);
            if (is_null($user)) {
                $user = $this->userRepository->create([
                    'full_name' => $dto->fullName,
                    'username' => $email,
                    'landline_number' => $dto->landlineNumber,
                    'phone_number' => $dto->phoneNumber,
                    'status_id' => UserStatus::ACTIVE,
                    'email_verified_at' => $isPrivateEmail ? null : ($isEmailVerified ? Carbon::now() : null),
                    'entity_type_id' => EntityType::ADMIN, //TODO CONFIGURABLE
                ]);
            }

            $this->userOAuthRepository->create([
                'user_id' => $user->id,
                'name' => $dto->fullName,
                'provider' => OAuthProvider::APPLE,
                'provider_id' => $providerId,
                'provider_token' => $dto->providerToken,
            ]);

            $sodiumQuery->multipleCommit();

        } catch (\Throwable $th) {
            $sodiumQuery->multipleRollBack();

            throw $th;
        }

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return json_decode(json_encode($user));
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
        return json_decode(json_encode(array_merge(
            $user->toArray(),
            [
                'token' => [
                    'accessToken' => $token,
                    'accessExpiredAt' => $accessTokenExpiredAt,
                    'refreshToken' => $refreshToken,
                    'refreshExpiredAt' => $refreshTokenExpiredAt,
                ], ]
        )));
    }
}
