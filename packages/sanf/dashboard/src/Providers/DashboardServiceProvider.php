<?php

namespace Sanf\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
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
        $this->mergeConfigFrom(__DIR__ . '/../../config/web-partner.php', 'web-partner');
    }
}
