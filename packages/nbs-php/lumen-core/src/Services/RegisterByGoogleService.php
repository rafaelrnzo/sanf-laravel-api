<?php


namespace NbsPhp\Core\Services;


use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Enum\AuthProvider;
use NbsPhp\Core\Enum\EntityType;
use NbsPhp\Core\Enum\OAuthProvider;
use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Exceptions\OAuthEmailRequiredException;
use NbsPhp\Core\Exceptions\OAuthUserAlreadyBoundException;
use NbsPhp\Core\Jwt\JWTHelper;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Models\UserOAuthModel;
use NbsPhp\Core\Models\UserSessionModel;

class RegisterByGoogleService implements RegisterByGoogleServiceInterface
{
    protected $jwt;

    protected $repository;

    public function __construct(JWTHelper $jwt, AuthModel $repository) //TODO USE REPOSITORY
    {
        $this->jwt = $jwt;
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $jwtPayload = $this->jwt::verifyGoogleToken($dto->providerToken);
        $email = $jwtPayload['email'];
        $providerId = $jwtPayload['sub'];
        $isEmailVerified = $jwtPayload['email_verified'] ?? false;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new OAuthEmailRequiredException('invalid email format');
        }
        //TODO REPOSITORY
        $user = DB::transaction(function () use ($dto, $email, $providerId, $isEmailVerified) {
            $userOAuth = UserOAuthModel::with('user')
                ->where([
                    'provider' => OAuthProvider::GOOGLE,
                    'provider_id' => $providerId,
                ])
                ->first();

            if ($userOAuth) {
                throw new OAuthUserAlreadyBoundException();
            }

            /** @var AuthModel $user */
            $user = $this->repository->newQuery()->where('username', $email)->first();
            if (is_null($user)) {
                $user = $this->repository->newQuery()->forceCreate([
                    'full_name' => $dto->fullName,
                    'username' => $email,
                    'landline_number' => $dto->landlineNumber,
                    'phone_number' => $dto->phoneNumber,
                    'status_id' => UserStatus::ACTIVE,
                    'email_verified_at' => $isEmailVerified ? Carbon::now() : null,
                    'entity_type_id' => EntityType::ADMIN, //TODO CONFIGURABLE
                ]);
            }

            UserOAuthModel::forceCreate([
                'user_id' => $user->id,
                'name' => $dto->fullName,
                'provider' => OAuthProvider::GOOGLE,
                'provider_id' => $providerId,
                'provider_token' => $dto->providerToken,
            ]);

            return $user;
        });

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
        return json_decode(json_encode(array_merge($user->toArray(), [
                'token' => [
                    'accessToken' => $token,
                    'accessExpiredAt' => $accessTokenExpiredAt,
                    'refreshToken' => $refreshToken,
                    'refreshExpiredAt' => $refreshTokenExpiredAt,
                ]]
        )));
    }
}
