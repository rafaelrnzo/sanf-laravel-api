<?php

namespace Sanf\Integration;

use Illuminate\Support\ServiceProvider;
use NbsPhp\ApiWrapper\ApiWrapper;

class IntegrationServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        ApiWrapper::load(__DIR__ . '/Modules/SanfCore/sanf-routes.php');
        ApiWrapper::load(__DIR__ . '/Modules/TekenAja/tekenaja-routes.php');
        ApiWrapper::load(__DIR__ . '/Modules/Scanina/scanina-routes.php');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfigs();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sanf-internal.php', 'sanf-internal');
        $this->mergeConfigFrom(__DIR__ . '/../config/tekenaja-internal.php', 'tekenaja-internal');
    }
}
