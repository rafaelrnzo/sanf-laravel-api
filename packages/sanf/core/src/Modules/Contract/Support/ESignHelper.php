<?php

namespace Sanf\Core\Modules\Contract\Support;

class ESignHelper
{
    public static function checkSignStatusCacheKey(string $userId, string $documentId): string
    {
        return "ESIGN::CHECK_SIGN_STATUS_RETRY:{$userId}:{$documentId}";
    }

    /**
     * Sign status check interval in seconds.
     */
    public static function signStatusCheckInterval(): int
    {
        return config('e-sign.sign_status_check_interval');
    }

    /**
     * Sign status check window expired in minutes.
     */
    public static function signStatusCheckWindowExpired(): int
    {
        return config('e-sign.sign_status_check_window_expired');
    }

    public static function isStatusCheckWindowExpired(int $currentTimestamp, int $signedTimestamp)
    {
        $expiredMinutes = static::signStatusCheckWindowExpired();
        $expiredAt = $signedTimestamp + ($expiredMinutes * 60);

        return $expiredAt > $currentTimestamp;
    }
}
