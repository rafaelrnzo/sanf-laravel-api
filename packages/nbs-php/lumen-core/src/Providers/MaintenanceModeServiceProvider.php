<?php

namespace NbsPhp\Core\Providers;

use Illuminate\Support\ServiceProvider;
use NbsPhp\Core\Commands\DownMaintenanceCommand;
use NbsPhp\Core\Commands\UpMaintenanceCommand;
use NbsPhp\Core\Middleware\MaintenanceModeMiddleware;
use NbsPhp\Core\Services\MaintenanceModeService;

class MaintenanceModeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->middleware([
            MaintenanceModeMiddleware::class,
        ]);

        $this->app->singleton('maintenance', function () {
            return new MaintenanceModeService($this->app);
        });

        $this->app->singleton('command.up', function () {
            return new UpMaintenanceCommand($this->app['maintenance']);
        });

        $this->app->singleton('command.down', function () {
            return new DownMaintenanceCommand($this->app['maintenance']);
        });

        $this->commands(['command.up', 'command.down']);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.up', 'command.down'];
    }
}
