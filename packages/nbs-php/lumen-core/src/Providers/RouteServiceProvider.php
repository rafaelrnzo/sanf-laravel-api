<?php

namespace NbsPhp\Core\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    private $namespace = 'NbsPhp\Core\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->router->group([
            'namespace' => $this->namespace,
        ], function ($router) {
            require __DIR__ . '/../routes.php';
        });
    }
}
