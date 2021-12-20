<?php

namespace Sanf\Core\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use NbsPhp\Core\Database\IlluminateSession;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use Sanf\Core\Modules\Astra\EloquentProductAstraRepository;
use Sanf\Core\Modules\Astra\ProductAstraRepositoryInterface;
use Sanf\Core\Modules\Branch\BranchRepositoryInterface;
use Sanf\Core\Modules\Branch\EloquentBranchRepository;
use Sanf\Core\Modules\Commodity\Repositories\CommodityRepositoryInterface;
use Sanf\Core\Modules\Commodity\Repositories\EloquentCommodityRepository;
use Sanf\Core\Modules\Commodity\Specifications\CommoditySpecificationFactoryInterface;
use Sanf\Core\Modules\Commodity\Specifications\EloquentCommoditySpecificationFactory;
use Sanf\Core\Modules\ContactUs\AskUsRepositoryInterface;
use Sanf\Core\Modules\ContactUs\AskUsTopicRepositoryInterface;
use Sanf\Core\Modules\ContactUs\EloquentAskUsRepository;
use Sanf\Core\Modules\ContactUs\EloquentAskUsTopicRepository;
use Sanf\Core\Modules\Financing\Repositories\EloquentFinancingApplicationRepository;
use Sanf\Core\Modules\Financing\Repositories\EloquentFinancingFacilityRepository;
use Sanf\Core\Modules\Financing\Repositories\EloquentFinancingMethodRepository;
use Sanf\Core\Modules\Financing\Repositories\EloquentFinancingPrerequisiteRepository;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingFacilityRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\EloquentFinancingApplicationSpecificationFactory;
use Sanf\Core\Modules\Financing\Specifications\EloquentFinancingFacilitySpecificationFactory;
use Sanf\Core\Modules\Financing\Specifications\EloquentFinancingMethodSpecificationFactory;
use Sanf\Core\Modules\Financing\Specifications\EloquentFinancingPrerequisiteSpecificationFactory;
use Sanf\Core\Modules\Financing\Specifications\FinancingApplicationSpecificationFactoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingFacilitySpecificationFactoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingMethodSpecificationFactoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingPrerequisiteSpecificationFactoryInterface;
use Sanf\Core\Modules\Location\EloquentLocationRepository;
use Sanf\Core\Modules\Location\LocationRepositoryInterface;
use Sanf\Core\Modules\News\EloquentNewsRepository;
use Sanf\Core\Modules\News\NewsRepositoryInterface;
use Sanf\Core\Modules\Plafond\Repositories\EloquentPlafondTypeRepository;
use Sanf\Core\Modules\Plafond\Repositories\GuzzleAndEloquentPlafondRepository;
use Sanf\Core\Modules\Plafond\Repositories\PlafondRepositoryInterface;
use Sanf\Core\Modules\Plafond\Repositories\PlafondTypeRepositoryInterface;
use Sanf\Core\Modules\Prepayment\Repositories\EloquentPrepaymentSubmissionRepository;
use Sanf\Core\Modules\Prepayment\Repositories\PrepaymentSubmissionRepositoryInterface;
use Sanf\Core\Modules\Product\EloquentProductRepository;
use Sanf\Core\Modules\Product\ProductRepositoryInterface;
use Sanf\Core\Modules\Project\Repositories\EloquentProjectRepository;
use Sanf\Core\Modules\Project\Repositories\ProjectRepositoryInterface;
use Sanf\Core\Modules\Project\Specifications\EloquentProjectSpecificationFactory;
use Sanf\Core\Modules\Project\Specifications\ProjectSpecificationFactoryInterface;
use Sanf\Core\Modules\Promo\EloquentPromoRepository;
use Sanf\Core\Modules\Promo\PromoRepositoryInterface;
use Sanf\Core\Modules\Staff\EloquentStaffRepository;
use Sanf\Core\Modules\Staff\StaffRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\EloquentUserRepository;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

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
        $this->app->register(EventServiceProvider::class);
    }

    public function registerBindings()
    {
        $this->app->bind(TransactionalSessionInterface::class, IlluminateSession::class);

        //REPOSITORY
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(AskUsTopicRepositoryInterface::class, EloquentAskUsTopicRepository::class);
        $this->app->bind(AskUsRepositoryInterface::class, EloquentAskUsRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, EloquentBranchRepository::class);
        $this->app->bind(NewsRepositoryInterface::class, EloquentNewsRepository::class);
        $this->app->bind(PromoRepositoryInterface::class, EloquentPromoRepository::class);
        $this->app->bind(ProductAstraRepositoryInterface::class, EloquentProductAstraRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, EloquentLocationRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, EloquentProjectRepository::class);
        $this->app->bind(CommodityRepositoryInterface::class, EloquentCommodityRepository::class);
        $this->app->bind(StaffRepositoryInterface::class, EloquentStaffRepository::class);
        $this->app->bind(FinancingMethodRepositoryInterface::class,EloquentFinancingMethodRepository::class);
        $this->app->bind(FinancingPrerequisiteRepositoryInterface::class,EloquentFinancingPrerequisiteRepository::class);
        $this->app->bind(FinancingFacilityRepositoryInterface::class,EloquentFinancingFacilityRepository::class);
        $this->app->bind(FinancingApplicationRepositoryInterface::class,EloquentFinancingApplicationRepository::class);
        $this->app->bind(PlafondTypeRepositoryInterface::class,EloquentPlafondTypeRepository::class);
        $this->app->bind(PlafondRepositoryInterface::class,GuzzleAndEloquentPlafondRepository::class);
        $this->app->bind(PrepaymentSubmissionRepositoryInterface::class,EloquentPrepaymentSubmissionRepository::class);

        //SPECIFICATION FACTORY
        $this->app->bind(ProjectSpecificationFactoryInterface::class, EloquentProjectSpecificationFactory::class);
        $this->app->bind(CommoditySpecificationFactoryInterface::class, EloquentCommoditySpecificationFactory::class);
        $this->app->bind(FinancingMethodSpecificationFactoryInterface::class, EloquentFinancingMethodSpecificationFactory::class);
        $this->app->bind(FinancingPrerequisiteSpecificationFactoryInterface::class,EloquentFinancingPrerequisiteSpecificationFactory::class);
        $this->app->bind(FinancingFacilitySpecificationFactoryInterface::class,EloquentFinancingFacilitySpecificationFactory::class);
        $this->app->bind(FinancingApplicationSpecificationFactoryInterface::class, EloquentFinancingApplicationSpecificationFactory::class);
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
