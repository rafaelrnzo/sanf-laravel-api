<?php

namespace Sanf\Core\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Sanf\Core\Modules\Astra\EloquentProductAstraRepository;
use Sanf\Core\Modules\Astra\ProductAstraRepositoryInterface;
use Sanf\Core\Modules\Branch\BranchRepositoryInterface;
use Sanf\Core\Modules\Branch\EloquentBranchRepository;
use Sanf\Core\Modules\ContactUs\AskUsRepositoryInterface;
use Sanf\Core\Modules\ContactUs\AskUsTopicRepositoryInterface;
use Sanf\Core\Modules\ContactUs\EloquentAskUsRepository;
use Sanf\Core\Modules\ContactUs\EloquentAskUsTopicRepository;
use Sanf\Core\Modules\News\EloquentNewsRepository;
use Sanf\Core\Modules\News\NewsRepositoryInterface;
use Sanf\Core\Modules\Promo\EloquentPromoRepository;
use Sanf\Core\Modules\Promo\PromoRepositoryInterface;
use Sanf\Core\Modules\Location\EloquentLocationRepository;
use Sanf\Core\Modules\Location\LocationRepositoryInterface;
use Sanf\Core\Modules\Product\EloquentProductRepository;
use Sanf\Core\Modules\Product\ProductRepositoryInterface;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadFactoriesFrom(__DIR__ . '/../../database/factories');

        Auth::provider('mobile-user', function ($app, array $config) {
            return new MobileUserProvider($app['hash'], $config['model']);
        });
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

    }

    public function registerBindings()
    {
//        $this->app->bind(FooRepositoryInterface::class, EloquentFooRepository::class);
        $this->app->bind(AskUsTopicRepositoryInterface::class, EloquentAskUsTopicRepository::class);
        $this->app->bind(AskUsRepositoryInterface::class, EloquentAskUsRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, EloquentBranchRepository::class);
        $this->app->bind(NewsRepositoryInterface::class, EloquentNewsRepository::class);
        $this->app->bind(PromoRepositoryInterface::class, EloquentPromoRepository::class);
        $this->app->bind(ProductAstraRepositoryInterface::class, EloquentProductAstraRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, EloquentLocationRepository::class);
    }

    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'core');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/image-path.php', 'image-path');
        $this->mergeConfigFrom(__DIR__ . '/../../config/sanf-mobile.php', 'sanf-mobile');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/vendor/core');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'core');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'core');
        }
    }
}
