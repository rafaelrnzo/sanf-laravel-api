<?php

namespace NbsPhp\Core\Providers;

use Laravel\Horizon\Horizon;

class HorizonServiceProvider extends \Laravel\Horizon\HorizonServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        $this->authorization();
    }

    /**
     * Overload authorization method from \Laravel\Horizon\HorizonApplicationServiceProvider
     * to allow access to Horizon without having a logged in user.
     *
     * @return void
     */
    protected function authorization()
    {
        Horizon::auth(function ($request) {
            return true;
        });
    }
}
