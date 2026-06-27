<?php

namespace NbsPhp\Core\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Enum\AuthProvider;
use NbsPhp\Core\Enum\OAuthProvider;
use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Exceptions\EmailUnverifiedException;
use NbsPhp\Core\Exceptions\EmptyEmailAtAppleAccountException;
use NbsPhp\Core\Exceptions\InvalidCredentialException;
use NbsPhp\Core\Exceptions\OAuthUserNotBoundException;
use NbsPhp\Core\Jwt\JWTHelper;
use NbsPhp\Core\Models\UserSessionModel;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\Enums\UserAuthLogStatusEnum;
use Sanf\Core\Modules\User\Repositories\UserAuthLogRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserOAuthRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class LoginByAppleService implements ApplicationServiceInterface
{
    protected $jwt;

    protected $repository;
    protected UserAuthLogRepositoryInterface $logRepository;
    protected UserOAuthRepositoryInterface $userOAuthRepository;

    public function __construct(
        JWTHelper $jwt,
        UserRepositoryInterface $repository,
        UserAuthLogRepositoryInterface $logRepository,
        UserOAuthRepositoryInterface $userOAuthRepository
    ) {
        $this->jwt = $jwt;
        $this->repository = $repository;
        $this->logRepository = $logRepository;
        $this->userOAuthRepository = $userOAuthRepository;
    }

    /**
     * @param null $dto
     * @return mixed
     */
    public function execute($dto = null)
    {
        $jwtPayload = $this->jwt::verifyAppleIdToken($dto->providerToken);

        $email = $jwtPayload['email'] ?? $dto->email;
        if (empty($email)) {
            throw new EmptyEmailAtAppleAccountException();
        }

        $providerId = $jwtPayload['sub'];
        $isEmailVerified = $jwtPayload['email_verified'] ?? false;
        $isPrivateEmail = $jwtPayload['is_private_email'] ?? true;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new OAuthUserNotBoundException('invalid email format');
        }

        return DB::transaction(function () use ($dto, $email, $isPrivateEmail, $isEmailVerified, $providerId) {
            //MATCH WITH EXISTING USER BY SAME EMAIL
            //SKIP IF EMAIL PRIVATE BECAUSE EMAIL NOT REAL FROM RELAY DOMAIN i.e: n7*****jh5@privaterelay.appleid.com
            //ALSO SKIP IF EMAIL STILL NOT VERIFIED
            $user = null;
            if (!$isPrivateEmail || $isEmailVerified) {
                $user = SodiumEncryption::query()->transaction(function () use ($email) {
                    return $this->repository->findByEmailAndStatusIds(
                        $email,
                        [UserStatus::ACTIVE, UserStatus::NEED_ACTIVATION]
                    );
                });

                if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
                    throw new EmailUnverifiedException();
                }
            }

            $userOAuth = $this->userOAuthRepository->findByProvider(OAuthProvider::APPLE, $providerId);

            if (!$user && !$userOAuth) {
                throw new OAuthUserNotBoundException();
            }
            if ($user && !$userOAuth) {
                $userOAuth = $this->userOAuthRepository->create([
                    'user_id' => $user->id,
                    'provider' => OAuthProvider::APPLE,
                    'provider_id' => $providerId,
                    'provider_token' => $dto->providerToken,
                ]);
            }

            $user = $userOAuth->user;
            $this->userOAuthRepository->update([
                'provider_token' => $dto->providerToken,
            ], $userOAuth->id);

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $token = Auth::login($user);

            $jwtToken = $this->jwt->setToken($token);
            $accessTokenExpiredAt = $jwtToken->getDecoded()->exp;
            $signature = $jwtToken->getDecoded()->jti;

            $device = $dto->device;

            $logApprovedDeletion = $this->logRepository->findByUserIdAndStatus($user->id, UserAuthLogStatusEnum::APPROVE);
            if ($logApprovedDeletion) {
                throw new InvalidCredentialException();
            }

            $logSubmittedDeletion = $this->logRepository->findByUserIdAndStatus($user->id, UserAuthLogStatusEnum::SUBMIT);
            if ($logSubmittedDeletion) {
                if (Carbon::parse(optional($logSubmittedDeletion)->restore_expired_at) < Carbon::now()) {
                    throw new InvalidCredentialException();
                }

                $this->logRepository->update([
                    'id' => $logSubmittedDeletion->id,
                    'status_id' => UserAuthLogStatusEnum::RESTORE,
                    'restore_expired_at' => null,
                    'created_by' => json_encode([
                        'type' => 10,
                        'user_id' => $user->id,
                        'full_name' => $user->full_name,
                    ]),
                ]);
            }

            //TODO REPOSITORY
            /** @var UserSessionModel $userSession */
            //TODO SESSION REPOSITORY
            $userSession = UserSessionModel::forceCreate([
                'auth_provider_id' => AuthProvider::APPLE,
                'user_id' => $user->id,
                'device_id' => $device->deviceId,
                'device_platform_id' => $device->devicePlatformId,
                'notification_channel_id' => $device->notificationChannelId ?? null,
                'notification_token' => $device->notificationToken ?? null,
                'device_metadata' => is_array($device->metadata ?? null) ? json_encode($device->metadata) : ($device->metadata ?? null),
                'device_user_agent' => is_array($device->metadata ?? null) ? ($device->metadata['user_agent'] ?? null) : null,
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
                'hasPassword' => isset($user->password),
            ])));
        });
    }
}
