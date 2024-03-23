<?php

namespace NbsPhp\Core\Services;

use Illuminate\Support\Facades\Auth;

class LogoutService implements ApplicationServiceInterface
{
    public function execute($dto = null): bool
    {
        try {
            Auth::logout();
        } catch (\Exception $e) {
            report($e);

            return false;
        }

        return true;
    }
}
