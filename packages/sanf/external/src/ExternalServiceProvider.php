<?php

namespace Sanf\External;

use Illuminate\Support\ServiceProvider;
use NbsPhp\ApiWrapper\ApiWrapper;

class ExternalServiceProvider extends ServiceProvider
{
    private $namespace = 'Sanf\External\Modules';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        ApiWrapper::load(__DIR__ . '/routes.php');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRoute();
    }

    /**
     * Register route.
     *
     * @return void
     */
    protected function registerRoute()
    {
        $this->app->router->group([
//            'middleware' => 'basic-auth',
            'namespace' => $this->namespace,
        ], function ($router) {
            require __DIR__ . '/routes.php';
        });
    }
}
