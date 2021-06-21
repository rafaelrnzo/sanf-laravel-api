<?php

namespace Sanf\Web\Providers;

use Illuminate\Support\ServiceProvider;

class WebServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'web');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfigs()
    {

    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/vendor/web');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'web');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'web');
        }
    }
}
