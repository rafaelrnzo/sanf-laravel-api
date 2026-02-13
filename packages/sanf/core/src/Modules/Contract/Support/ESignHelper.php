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
}
