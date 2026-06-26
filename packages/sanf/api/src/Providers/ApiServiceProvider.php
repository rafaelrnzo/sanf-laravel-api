<?php

namespace Sanf\Api\Providers;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
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
        $this->app->routeMiddleware([
            'inject-user-id' => \Sanf\Api\Middleware\InjectUserIdFromPathMiddleware::class,
            'ocr-api-key' => \Sanf\Api\Middleware\OcrApiKeyMiddleware::class,
        ]);
   }

    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'api');
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
        $langPath = resource_path('lang/vendor/api');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'api');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'api');
        }
    }
}
