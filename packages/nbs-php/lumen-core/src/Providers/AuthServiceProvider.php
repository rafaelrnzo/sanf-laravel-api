<?php

namespace NbsPhp\Core\Providers;

use Illuminate\Support\ServiceProvider;
use NbsPhp\Core\Repositories\ProfileRepositoryInterface;
use NbsPhp\Core\Repositories\UserRepositoryInterface;
use NbsPhp\Core\Services\ActivateUserServiceInterface;
use NbsPhp\Core\Services\RegisterByAppleServiceInterface;
use NbsPhp\Core\Services\RegisterByEmailServiceInterface;
use NbsPhp\Core\Services\RegisterByGoogleServiceInterface;
use NbsPhp\Core\Services\VerifyEmailServiceInterface;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRoutes();
        $this->registerBindings();
    }

    /**
     * Register routes.
     */
    private function registerRoutes()
    {
        collect(($routes = $this->getRoutes()))->each(function ($route) {
            $this->registerRoute($route);
        });
    }

    /**
     * Get the list of routes.
     *
     * @return array
     */
    private function getRoutes()
    {
        return config('auth.routes.list');
    }

    /**
     * @param $route
     * @param null $name
     */
    protected function registerRoute($route, $name = null)
    {
        $attributes = [
            'middleware' => isset($route['middleware']) ? $route['middleware'] : [],
        ];

        $this->getRouter()->group($attributes, function () use ($route, $name) {
            $action = isset($route['controller'])
                ? "{$route['controller']}@{$route['action']}"
                : $route['action'];

            $this->getRouter()->{$route['method']}($route['uri'], [
                'as' => $name ?: $route['name'],
                'uses' => $action,
            ]);
        });
    }

    protected function registerBindings()
    {
        $this->app->bind(ProfileRepositoryInterface::class, config('auth.repositories.profile'));
        $this->app->bind(UserRepositoryInterface::class, config('auth.repositories.user'));
        $this->app->bind(RegisterByEmailServiceInterface::class, config('auth.services.register-by-email'));
        $this->app->bind(RegisterByGoogleServiceInterface::class, config('auth.services.register-by-google'));
        $this->app->bind(RegisterByAppleServiceInterface::class, config('auth.services.register-by-apple'));
        $this->app->bind(ActivateUserServiceInterface::class, config('auth.services.activate-user'));
        $this->app->bind(VerifyEmailServiceInterface::class, config('auth.services.verify-email'));
    }

    /**
     * Get the current router.
     *
     * @return mixed
     */
    protected function getRouter()
    {
        return app()->router;
    }
}
