<?php

namespace NbsPhp\Core\Providers;

use Illuminate\Support\ServiceProvider;
use NbsPhp\Core\Jwt\JWTGuard;
use NbsPhp\Core\Jwt\JWTHelper;

class JWTAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function boot()
    {
        $this->app['auth']->extend('jwt-auth', function ($app, $name, array $config) {

            $guard = new JWTGuard(
                $name,
                new JWTHelper(),
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}
