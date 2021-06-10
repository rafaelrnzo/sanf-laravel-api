<?php

namespace NbsPhp\Core\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use NbsPhp\Core\Enum\AuthProvider;
use NbsPhp\Core\Exceptions\EmailAlreadyExistException;
use NbsPhp\Core\Exceptions\InvalidCredentialException;
use NbsPhp\Core\Exceptions\InvalidRefreshTokenException;
use NbsPhp\Core\Exceptions\InvalidTokenException;
use NbsPhp\Core\JWTHelper;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Models\UserSessionModel;

class AuthService extends AbstractGeneralService
{
    private $user;

    private $jwt;

    public function __construct(AuthModel $user, JWTHelper $jwt)
    {
        $this->user = $user;
        $this->jwt = $jwt;
    }
    public function loginWithGoogle(array $input): object
    {
        JWTHelper::verifyGoogleToken($input['auth_token']);

        return DB::transaction(function () use ($input) {
            /** @var UserOAuth $userOAuth */
            $userOAuth = UserOAuth::with('user')
                ->where([
                    'provider' => UserOAuth::PROVIDER_GOOGLE,
                    'provider_id' => $input['user_ref_id'],
                ])
                ->first();

            /** @var AuthModel $user */
            if ($userOAuth) {
                $user = $userOAuth->user;

                $userOAuth->update([
                    'name' => $input['full_name'],
                    'provider_token' => $input['auth_token'],
                ]);
            } else {
                $user = $this->user->newQuery()->firstOrCreate([
                    'email' => $input['email'],
                ], [
                    'name' => $input['full_name'],
                    'email' => $input['email'],
                    'telp' => $input['phone'] ?? null,
                    'password' => bcrypt(nano_id()),
                    'role' => AuthModel::ROLE_DONOR,
                ]);

                $user->userOAuths()->create([
                    'name' => $input['full_name'],
                    'provider' => UserOAuth::PROVIDER_GOOGLE,
                    'provider_id' => $input['user_ref_id'],
                    'provider_token' => $input['auth_token'],
                ]);

                if (!$user->hasVerifiedEmail()) {
                    $user->sendEmailVerificationNotification();
                }
            }

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $token = Auth::login($user);

            $jwtToken = $this->jwt->setToken($token);
            $accessTokenExpiredAt = $jwtToken->getDecoded()->exp;

            $device = $input['device'];
            $metadata = $device['metadata'] ?? [];

            /** @var UserSessionModel $userSession */
            $userSession = $this->insertUserSession([
                'user_id' => $user->id,
                'device_id' => $device['device_id'],
                'device_platform_id' => $device['device_platform_id'],
                'notification_channel_id' => $device['notification_channel'] ?? null,
                'notification_token' => $device['notification_token'] ?? null,
                'device_manufacturer' => $metadata['manufacturer'] ?? null,
                'device_model' => $metadata['model'] ?? null,
                'device_user_agent' => $metadata['user_agent'] ?? null,
                'signature' => null, // TODO: need confirmation value
                'expired_at' => $accessTokenExpiredAt,
            ]);

            $refreshToken = $jwtToken->getRefreshToken($userSession->id);
            $refreshTokenExpiredAt = $jwtToken->getDecodedRefreshToken()->exp;

            $this->insertUserSession([
                'user_id' => $user->id,
                'device_id' => $device['device_id'],
                'device_platform_id' => $device['device_platform_id'],
                'notification_channel_id' => $device['notification_channel'] ?? null,
                'notification_token' => $device['notification_token'] ?? null,
                'device_manufacturer' => $metadata['manufacturer'] ?? null,
                'device_model' => $metadata['model'] ?? null,
                'device_user_agent' => $metadata['user_agent'] ?? null,
                'signature' => null, // TODO: need confirmation value
                'expired_at' => $accessTokenExpiredAt,
            ]);

            return $this->sendAsObject(array_merge($user->toArray(), [
                'access_token' => $token,
                'access_expired_at' => $accessTokenExpiredAt,
                'refresh_token' => $refreshToken,
                'refresh_expired_at' => $refreshTokenExpiredAt,
            ]));
        });
    }

    public function loginWithFacebook(array $input): object
    {
        return DB::transaction(function () use ($input) {
            /** @var UserOAuth $userOAuth */
            $userOAuth = UserOAuth::with('user')
                ->where([
                    'provider' => UserOAuth::PROVIDER_FACEBOOK,
                    'provider_id' => $input['user_ref_id'],
                ])
                ->first();

            /** @var AuthModel $user */
            if ($userOAuth) {
                $user = $userOAuth->user;

                $userOAuth->update([
                    'name' => $input['full_name'],
                    'provider_token' => $input['auth_token'],
                ]);
            } else {
                $user = $this->user->newQuery()->firstOrCreate([
                    'email' => $input['email'],
                ], [
                    'name' => $input['full_name'],
                    'email' => $input['email'],
                    'telp' => $input['phone'] ?? null,
                    'password' => bcrypt(nano_id()),
                    'role' => AuthModel::ROLE_DONOR,
                ]);

                $user->userOAuths()->create([
                    'name' => $input['full_name'],
                    'provider' => UserOAuth::PROVIDER_FACEBOOK,
                    'provider_id' => $input['user_ref_id'],
                    'provider_token' => $input['auth_token'],
                ]);

                if (!$user->hasVerifiedEmail()) {
                    $user->sendEmailVerificationNotification();
                }
            }

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $token = Auth::login($user);

            $jwtToken = $this->jwt->setToken($token);
            $accessTokenExpiredAt = $jwtToken->getDecoded()->exp;

            $device = $input['device'];
            $metadata = $device['metadata'] ?? [];

            /** @var UserSessionModel $userSession */
            $userSession = $this->insertUserSession([
                'user_id' => $user->id,
                'device_id' => $device['device_id'],
                'device_platform_id' => $device['device_platform_id'],
                'notification_channel_id' => $device['notification_channel'] ?? null,
                'notification_token' => $device['notification_token'] ?? null,
                'device_manufacturer' => $metadata['manufacturer'] ?? null,
                'device_model' => $metadata['model'] ?? null,
                'device_user_agent' => $metadata['user_agent'] ?? null,
                'signature' => null, // TODO: need confirmation value
                'expired_at' => $accessTokenExpiredAt,
            ]);

            $refreshToken = $jwtToken->getRefreshToken($userSession->id);
            $refreshTokenExpiredAt = $jwtToken->getDecodedRefreshToken()->exp;

            $this->insertUserSession([
                'user_id' => $user->id,
                'device_id' => $device['device_id'],
                'device_platform_id' => $device['device_platform_id'],
                'notification_channel_id' => $device['notification_channel'] ?? null,
                'notification_token' => $device['notification_token'] ?? null,
                'device_manufacturer' => $metadata['manufacturer'] ?? null,
                'device_model' => $metadata['model'] ?? null,
                'device_user_agent' => $metadata['user_agent'] ?? null,
                'signature' => null, // TODO: need confirmation value
                'expired_at' => $accessTokenExpiredAt,
            ]);

            return $this->sendAsObject(array_merge($user->toArray(), [
                'access_token' => $token,
                'access_expired_at' => $accessTokenExpiredAt,
                'refresh_token' => $refreshToken,
                'refresh_expired_at' => $refreshTokenExpiredAt,
            ]));
        });
    }

    public function loginWithApple(array $input): object
    {
        $appleJWTToken = JWTHelper::verifyAppleIdToken($input['auth_token']);

        if ($appleJWTToken['is_private_email'] === 'true') {
            throw new EmailRequiredException();
        }

        return DB::transaction(function () use ($input, $appleJWTToken) {
            /** @var UserOAuth $userOAuth */
            $userOAuth = UserOAuth::with('user')
                ->where([
                    'provider' => UserOAuth::PROVIDER_APPLE,
                    'provider_id' => $appleJWTToken['sub'],
                ])
                ->first();

            /** @var AuthModel $user */
            if ($userOAuth) {
                $user = $userOAuth->user;

                $userOAuth->update([
                    'name' => $input['full_name'],
                    'provider_token' => $input['auth_token'],
                ]);
            } else {
                $user = $this->user->newQuery()->firstOrCreate([
                    'email' => $input['email'],
                ], [
                    'name' => $input['full_name'],
                    'email' => $input['email'],
                    'telp' => $input['phone'] ?? null,
                    'password' => bcrypt(nano_id()),
                    'role' => AuthModel::ROLE_DONOR,
                ]);

                $user->userOAuths()->create([
                    'name' => $input['full_name'],
                    'provider' => UserOAuth::PROVIDER_APPLE,
                    'provider_id' => $appleJWTToken['sub'],
                    'provider_token' => $input['auth_token'],
                ]);

                if (!$user->hasVerifiedEmail()) {
                    $user->sendEmailVerificationNotification();
                }
            }

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $token = Auth::login($user);

            $jwtToken = $this->jwt->setToken($token);
            $accessTokenExpiredAt = $jwtToken->getDecoded()->exp;

            $device = $input['device'];
            $metadata = $device['metadata'] ?? [];

            /** @var UserSessionModel $userSession */
            $userSession = $this->insertUserSession([
                'user_id' => $user->id,
                'device_id' => $device['device_id'],
                'device_platform_id' => $device['device_platform_id'],
                'notification_channel_id' => $device['notification_channel'] ?? null,
                'notification_token' => $device['notification_token'] ?? null,
                'device_manufacturer' => $metadata['manufacturer'] ?? null,
                'device_model' => $metadata['model'] ?? null,
                'device_user_agent' => $metadata['user_agent'] ?? null,
                'signature' => null, // TODO: need confirmation value
                'expired_at' => $accessTokenExpiredAt,
            ]);

            $refreshToken = $jwtToken->getRefreshToken($userSession->id);
            $refreshTokenExpiredAt = $jwtToken->getDecodedRefreshToken()->exp;

            $this->insertUserSession([
                'user_id' => $user->id,
                'device_id' => $device['device_id'],
                'device_platform_id' => $device['device_platform_id'],
                'notification_channel_id' => $device['notification_channel'] ?? null,
                'notification_token' => $device['notification_token'] ?? null,
                'device_manufacturer' => $metadata['manufacturer'] ?? null,
                'device_model' => $metadata['model'] ?? null,
                'device_user_agent' => $metadata['user_agent'] ?? null,
                'signature' => null, // TODO: need confirmation value
                'expired_at' => $accessTokenExpiredAt,
            ]);

            return $this->sendAsObject(array_merge($user->toArray(), [
                'access_token' => $token,
                'access_expired_at' => $accessTokenExpiredAt,
                'refresh_token' => $refreshToken,
                'refresh_expired_at' => $refreshTokenExpiredAt,
            ]));
        });
    }

    public function forgotPassword(array $input)
    {
        Password::sendResetLink($input);
    }

    public function resetPassword(array $input)
    {
        $status = Password::reset($input, function ($user, $password) {
            $user->update(['password' => bcrypt($password)]);
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw new InvalidTokenException;
        }
    }

    public function updateUserSession(array $input, string $refreshToken)
    {
        $jwtRefreshToken = $this->jwt->setToken($refreshToken);

        if (empty($sessionId = optional($jwtRefreshToken->getDecoded())->sub)) {
            throw new InvalidRefreshTokenException;
        }

        /** @var UserSessionModel $session */
        $session = UserSessionModel::query()->findOrFail($sessionId);

        /** @var AuthModel $user */
        $user = $this->user->newQuery()->find($session->user_id);

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $token = Auth::login($user);

        $jwtToken = $this->jwt->setToken($token);
        $accessTokenExpiredAt = $jwtToken->getDecoded()->exp;

        $metadata = $input['metadata'] ?? [];

        $session->update([
            'user_id' => $user->id,
            'device_id' => $input['device_id'],
            'device_platform_id' => $input['device_platform_id'],
            'notification_channel_id' => $input['notification_channel'] ?? null,
            'notification_token' => $input['notification_token'] ?? null,
            'device_manufacturer' => $metadata['manufacturer'] ?? null,
            'device_model' => $metadata['model'] ?? null,
            'device_user_agent' => $metadata['user_agent'] ?? null,
            'signature' => null, // TODO: need confirmation value
            'expired_at' => $accessTokenExpiredAt,
        ]);

        $refreshToken = $jwtToken->getRefreshToken($session->id);
        $refreshTokenExpiredAt = $jwtToken->getDecodedRefreshToken()->exp;

        return $this->sendAsObject(array_merge($user->toArray(), [
            'access_token' => $token,
            'access_expired_at' => $accessTokenExpiredAt,
            'refresh_token' => $refreshToken,
            'refresh_expired_at' => $refreshTokenExpiredAt,
        ]));
    }

    public function checkEmailAvailability(array $input): bool
    {
        return $this->user->newQuery()->where('email', $input['email'])->count('id') > 0;
    }

    private function insertUserSession($data)
    {
        return UserSessionModel::query()->create([
            'user_id' => $data['user_id'],
            'auth_provider_id' => $data['auth_provider_id'] ?? AuthProvider::APP,
            'device_platform_id' => $data['device_platform_id'],
            'device_id' => $data['device_id'],
            'device_manufacturer' => $data['device_manufacturer'],
            'device_model' => $data['device_model'],
            'device_user_agent' => $data['device_user_agent'],
            'notification_channel_id' => $data['notification_channel_id'],
            'notification_token' => $data['notification_token'],
            'signature' => $data['signature'],
            'expired_at' => Carbon::parse($data['expired_at'])->toDateTimeString(),
        ]);
    }

    public function verificationEmail($id, $token)
    {
        /** @var AuthModel $user */
        $user = $this->user->newQuery()->findOrFail($id);

        if (!hash_equals((string) $token, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        return DB::transaction(function () use ($id, $token, $user) {
            $user->markEmailAsVerified();
        });
    }
}
