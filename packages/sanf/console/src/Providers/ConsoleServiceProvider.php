<?php

namespace Sanf\Console\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
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
        $this->registerTranslations();
        $this->registerViews();
        $this->registerBindings();
        $this->registerProviders();
    }

    public function registerProviders()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    public function registerBindings()
    {
//        $this->app->bind(FooRepositoryInterface::class, EloquentFooRepository::class);
   }

    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'console');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/response-codes.php', 'response-codes');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/vendor/console');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'console');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'console');
        }
    }
}
