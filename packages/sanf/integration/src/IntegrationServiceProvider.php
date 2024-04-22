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
        ApiWrapper::load(__DIR__ . '/Modules/Nanonets/nanonets-routes.php');
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
        $this->mergeConfigFrom(__DIR__ . '/../config/sanf-api.php', 'sanf-api');
        $this->mergeConfigFrom(__DIR__ . '/../config/tekenaja-api.php', 'tekenaja-api');
        $this->mergeConfigFrom(__DIR__ . '/../config/scanina-api.php', 'scanina-api');
        $this->mergeConfigFrom(__DIR__ . '/../config/nanonets-api.php', 'nanonets-api');
    }
}
