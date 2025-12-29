<?php

namespace Sanf\Core\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use NbsPhp\Core\Database\IlluminateSession;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use Sanf\Core\Caches\DatabaseSodiumStore;
use Sanf\Core\CheckPicMiddleware;
use Sanf\Core\Database\IlluminateMultipleSession;
use Sanf\Core\Database\MultipleTransactionalSessionInterface;
use Sanf\Core\Modules\Astra\EloquentProductAstraRepository;
use Sanf\Core\Modules\Astra\ProductAstraRepositoryInterface;
use Sanf\Core\Modules\Branch\BranchRepositoryInterface;
use Sanf\Core\Modules\Branch\EloquentBranchRepository;
use Sanf\Core\Modules\Commodity\Repositories\CommodityRepositoryInterface;
use Sanf\Core\Modules\Commodity\Repositories\EloquentCommodityEncryptedRepository;
use Sanf\Core\Modules\Commodity\Specifications\CommoditySpecificationFactoryInterface;
use Sanf\Core\Modules\Commodity\Specifications\EloquentCommoditySpecificationFactory;
use Sanf\Core\Modules\ContactUs\AskUsRepositoryInterface;
use Sanf\Core\Modules\ContactUs\AskUsTopicRepositoryInterface;
use Sanf\Core\Modules\ContactUs\EloquentAskUsEncryptedRepository;
use Sanf\Core\Modules\ContactUs\EloquentAskUsTopicRepository;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentEncryptedRepository;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignEncryptedRepository;
use Sanf\Core\Modules\Contract\Repositories\EloquentFinancingUnitLocationSubmissionEncryptedRepository;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\Contract\Repositories\FinancingUnitLocationSubmissionRepositoryInterface;
use Sanf\Core\Modules\Contract\Specifications\EloquentESignDocumentSpecificationFactory;
use Sanf\Core\Modules\Contract\Specifications\EloquentFinancingUnitLocationSubmissionSpecificationFactory;
use Sanf\Core\Modules\Contract\Specifications\ESignDocumentSpecificationFactoryInterface;
use Sanf\Core\Modules\Contract\Specifications\FinancingUnitLocationSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\Disbursement\Repositories\SparePartDisbursementEloquentRepository;
use Sanf\Core\Modules\Disbursement\Repositories\SparePartDisbursementRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\EloquentFinancingApplicationEncryptedRepository;
use Sanf\Core\Modules\Financing\Repositories\EloquentFinancingCategoryRepository;
use Sanf\Core\Modules\Financing\Repositories\EloquentFinancingFacilityRepository;
use Sanf\Core\Modules\Financing\Repositories\EloquentFinancingMethodRepository;
use Sanf\Core\Modules\Financing\Repositories\EloquentFinancingPrerequisiteRepository;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingCategoryRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingFacilityRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\EloquentFinancingApplicationSpecificationFactory;
use Sanf\Core\Modules\Financing\Specifications\EloquentFinancingCategorySpecificationFactory;
use Sanf\Core\Modules\Financing\Specifications\EloquentFinancingFacilitySpecificationFactory;
use Sanf\Core\Modules\Financing\Specifications\EloquentFinancingMethodSpecificationFactory;
use Sanf\Core\Modules\Financing\Specifications\EloquentFinancingPrerequisiteSpecificationFactory;
use Sanf\Core\Modules\Financing\Specifications\FinancingApplicationSpecificationFactoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingCategorySpecificationFactoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingFacilitySpecificationFactoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingMethodSpecificationFactoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingPrerequisiteSpecificationFactoryInterface;
use Sanf\Core\Modules\HttpLog\Repositories\ApiRequestRepositoryInterface;
use Sanf\Core\Modules\HttpLog\Repositories\AuditHttpLogRepositoryInterface;
use Sanf\Core\Modules\HttpLog\Repositories\EloquentApiRequestLogEncryptedRepository;
use Sanf\Core\Modules\HttpLog\Repositories\EloquentAuditHttpLogEncryptedRepository;
use Sanf\Core\Modules\Installment\Repositories\InstallmentEloquentRepository;
use Sanf\Core\Modules\Installment\Repositories\InstallmentRepositoryInterface;
use Sanf\Core\Modules\Insurance\Repositories\EloquentInsuranceClaimSubmissionEncryptedRepository;
use Sanf\Core\Modules\Insurance\Repositories\InsuranceClaimSubmissionRepositoryInterface;
use Sanf\Core\Modules\Insurance\Specifications\EloquentInsuranceClaimSubmissionSpecificationFactory;
use Sanf\Core\Modules\Insurance\Specifications\InsuranceClaimSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\Invoice\Repositories\EloquentInvoiceCollectionSubmissionEncryptedRepository;
use Sanf\Core\Modules\Invoice\Repositories\InvoiceCollectionSubmissionRepositoryInterface;
use Sanf\Core\Modules\Invoice\Specifications\EloquentInvoiceCollectionSubmissionSpecificationFactory;
use Sanf\Core\Modules\Invoice\Specifications\InvoiceCollectionSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\Location\EloquentLocationEncryptedRepository;
use Sanf\Core\Modules\Location\LocationRepositoryInterface;
use Sanf\Core\Modules\Log\Repositories\WebhookLogEloquentRepository;
use Sanf\Core\Modules\Log\Repositories\WebhookLogRepositoryInterface;
use Sanf\Core\Modules\News\EloquentNewsRepository;
use Sanf\Core\Modules\News\NewsRepositoryInterface;
use Sanf\Core\Modules\Payment\Repositories\PaymentEloquentRepository;
use Sanf\Core\Modules\Payment\Repositories\PaymentPreviewEloquentRepository;
use Sanf\Core\Modules\Payment\Repositories\PaymentPreviewRepositoryInterface;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldEloquentRepository;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldGiroEloquentRepository;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldGiroRepositoryInterface;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldRepositoryInterface;
use Sanf\Core\Modules\PdcHold\Specifications\EloquentPdcHoldSubmissionSpecificationFactory;
use Sanf\Core\Modules\PdcHold\Specifications\PdcHoldSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\Plafond\Repositories\EloquentPlafondTypeRepository;
use Sanf\Core\Modules\Plafond\Repositories\GuzzleAndEloquentPlafondRepository;
use Sanf\Core\Modules\Plafond\Repositories\PaymentAccelarationDocumentEncryptedEloquentRepository;
use Sanf\Core\Modules\Plafond\Repositories\PaymentAccelarationDocumentRepositoryInterface;
use Sanf\Core\Modules\Plafond\Repositories\PlafondDisbursementEncryptedEloquentRepository;
use Sanf\Core\Modules\Plafond\Repositories\PlafondDisbursementRepositoryInterface;
use Sanf\Core\Modules\Plafond\Repositories\PlafondRepositoryInterface;
use Sanf\Core\Modules\Plafond\Repositories\PlafondTypeRepositoryInterface;
use Sanf\Core\Modules\Prepayment\Repositories\EloquentPrepaymentSubmissionRepository;
use Sanf\Core\Modules\Prepayment\Repositories\PrepaymentSubmissionRepositoryInterface;
use Sanf\Core\Modules\Product\EloquentProductRepository;
use Sanf\Core\Modules\Product\ProductRepositoryInterface;
use Sanf\Core\Modules\Project\Repositories\EloquentProjectEncryptedRepository;
use Sanf\Core\Modules\Project\Repositories\ProjectRepositoryInterface;
use Sanf\Core\Modules\Project\Specifications\EloquentProjectSpecificationFactory;
use Sanf\Core\Modules\Project\Specifications\ProjectSpecificationFactoryInterface;
use Sanf\Core\Modules\Promo\EloquentPromoRepository;
use Sanf\Core\Modules\Promo\PromoRepositoryInterface;
use Sanf\Core\Modules\RequestedDocument\Repositories\EloquentRequestedDocumentItemEncryptedRepository;
use Sanf\Core\Modules\RequestedDocument\Repositories\EloquentRequestedDocumentRepository;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentItemRepositoryInterface;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentRepositoryInterface;
use Sanf\Core\Modules\RequestedDocument\Specifications\EloquentRequestedDocumentSpecification;
use Sanf\Core\Modules\RequestedDocument\Specifications\RequestedDocumentSpecificationInterface;
use Sanf\Core\Modules\Scanina\Repositories\EloquentProductCartEncryptedRepository;
use Sanf\Core\Modules\Scanina\Repositories\EloquentScaninaUserRegistrationEncryptedRepository;
use Sanf\Core\Modules\Scanina\Repositories\GuzzleScaninaProductRepository;
use Sanf\Core\Modules\Scanina\Repositories\GuzzleScaninaRegionRepository;
use Sanf\Core\Modules\Scanina\Repositories\GuzzleScaninaUserRepository;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaRegionRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaUserRegistrationRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaUserRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\EloquentProductCartSpecification;
use Sanf\Core\Modules\Scanina\Specifications\GuzzleScaninaProductSpecification;
use Sanf\Core\Modules\Scanina\Specifications\GuzzleScaninaRegionSpecification;
use Sanf\Core\Modules\Scanina\Specifications\GuzzleScaninaUserSpecification;
use Sanf\Core\Modules\Scanina\Specifications\ProductCartSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaRegionSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaUserSpecificationInterface;
use Sanf\Core\Modules\Setting\Repositories\EloquentFrequentlyAskQuestionRepository;
use Sanf\Core\Modules\Setting\Repositories\EloquentOnBoardingRepository;
use Sanf\Core\Modules\Setting\Repositories\EloquentStaticContentRepository;
use Sanf\Core\Modules\Setting\Repositories\FrequentlyAskQuestionRepositoryInterface;
use Sanf\Core\Modules\Setting\Repositories\OnBoardingRepositoryInterface;
use Sanf\Core\Modules\Setting\Repositories\StaticContentRepositoryInterface;
use Sanf\Core\Modules\Setting\Specifications\EloquentFrequentlyAskQuestionSpecificationFactory;
use Sanf\Core\Modules\Setting\Specifications\EloquentOnBoardingSpecificationFactory;
use Sanf\Core\Modules\Setting\Specifications\FrequentlyAskQuestionSpecificationFactoryInterface;
use Sanf\Core\Modules\Setting\Specifications\OnBoardingSpecificationFactoryInterface;
use Sanf\Core\Modules\Staff\EloquentStaffRepository;
use Sanf\Core\Modules\Staff\StaffRepositoryInterface;
use Sanf\Core\Modules\Survey\Entities\EloquentSurveyFactoryEntity;
use Sanf\Core\Modules\Survey\Entities\SurveyEntityFactoryInterface;
use Sanf\Core\Modules\Survey\Repositories\EloquentSurveyEncryptedRepository;
use Sanf\Core\Modules\Survey\Repositories\SurveyRepositoryInterface;
use Sanf\Core\Modules\Survey\Specifications\EloquentSurveySpecificationFactory;
use Sanf\Core\Modules\Survey\Specifications\SurveySpecificationFactoryInterface;
use Sanf\Core\Modules\User\Repositories\EloquentUserAuthLogEncryptedRepository;
use Sanf\Core\Modules\User\Repositories\EloquentUserEncryptedRepository;
use Sanf\Core\Modules\User\Repositories\EloquentUserOAuthEncryptedRepository;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\RestProfileRepository;
use Sanf\Core\Modules\User\Repositories\UserAuthLogRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserOAuthRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Specifications\EloquentUserAuthEncryptedSpecificationFactory;
use Sanf\Core\Modules\User\Specifications\EloquentUserAuthLogEncryptedSpecificationFactory;
use Sanf\Core\Modules\User\Specifications\UserAuthLogSpecificationFactoryInterface;
use Sanf\Core\Modules\User\Specifications\UserAuthSpecificationFactoryInterface;
use Sanf\Core\Passwords\SodiumPasswordBrokerManager;

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

        Auth::provider('eloquent-mobile-user-provider', function ($app, array $config) {
            return new EloquentMobileUserProvider($app['hash'], $config['model']);
        });

        Cache::extend('database_sodium', function ($app, array $config) {
            return Cache::repository(new DatabaseSodiumStore($app, $config));
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

        $this->app->routeMiddleware([
            'pic' => CheckPicMiddleware::class,
        ]);
    }

    public function registerProviders()
    {
        $this->app->register(EventServiceProvider::class);
    }

    public function registerBindings()
    {
        $this->app->bind(TransactionalSessionInterface::class, IlluminateSession::class);
        $this->app->bind(MultipleTransactionalSessionInterface::class, IlluminateMultipleSession::class);

        //REPOSITORY
        $this->app->bind(UserRepositoryInterface::class, EloquentUserEncryptedRepository::class);
        $this->app->bind(AskUsTopicRepositoryInterface::class, EloquentAskUsTopicRepository::class);
        $this->app->bind(AskUsRepositoryInterface::class, EloquentAskUsEncryptedRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, EloquentBranchRepository::class);
        $this->app->bind(NewsRepositoryInterface::class, EloquentNewsRepository::class);
        $this->app->bind(PromoRepositoryInterface::class, EloquentPromoRepository::class);
        $this->app->bind(ProductAstraRepositoryInterface::class, EloquentProductAstraRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, EloquentLocationEncryptedRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, EloquentProjectEncryptedRepository::class);
        $this->app->bind(CommodityRepositoryInterface::class, EloquentCommodityEncryptedRepository::class);
        $this->app->bind(StaffRepositoryInterface::class, EloquentStaffRepository::class);
        $this->app->bind(FinancingMethodRepositoryInterface::class, EloquentFinancingMethodRepository::class);
        $this->app->bind(FinancingPrerequisiteRepositoryInterface::class, EloquentFinancingPrerequisiteRepository::class);
        $this->app->bind(FinancingFacilityRepositoryInterface::class, EloquentFinancingFacilityRepository::class);
        $this->app->bind(FinancingApplicationRepositoryInterface::class, EloquentFinancingApplicationEncryptedRepository::class);
        $this->app->bind(PlafondTypeRepositoryInterface::class, EloquentPlafondTypeRepository::class);
        $this->app->bind(PlafondRepositoryInterface::class, GuzzleAndEloquentPlafondRepository::class);
        $this->app->bind(PrepaymentSubmissionRepositoryInterface::class, EloquentPrepaymentSubmissionRepository::class);
        $this->app->bind(FinancingUnitLocationSubmissionRepositoryInterface::class, EloquentFinancingUnitLocationSubmissionEncryptedRepository::class);
        $this->app->bind(InvoiceCollectionSubmissionRepositoryInterface::class, EloquentInvoiceCollectionSubmissionEncryptedRepository::class);
        $this->app->bind(InsuranceClaimSubmissionRepositoryInterface::class, EloquentInsuranceClaimSubmissionEncryptedRepository::class);
        $this->app->bind(SurveyRepositoryInterface::class, EloquentSurveyEncryptedRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, RestProfileRepository::class);
        $this->app->bind(ESignRepositoryInterface::class, EloquentESignEncryptedRepository::class);
        $this->app->bind(OnBoardingRepositoryInterface::class, EloquentOnBoardingRepository::class);
        $this->app->bind(FrequentlyAskQuestionRepositoryInterface::class, EloquentFrequentlyAskQuestionRepository::class);
        $this->app->bind(StaticContentRepositoryInterface::class, EloquentStaticContentRepository::class);
        $this->app->bind(UserAuthLogRepositoryInterface::class, EloquentUserAuthLogEncryptedRepository::class);
        $this->app->bind(FinancingCategoryRepositoryInterface::class, EloquentFinancingCategoryRepository::class);
        $this->app->bind(ProductCartRepositoryInterface::class, EloquentProductCartEncryptedRepository::class);
        $this->app->bind(ScaninaUserRegistrationRepositoryInterface::class, EloquentScaninaUserRegistrationEncryptedRepository::class);
        $this->app->bind(ScaninaProductRepositoryInterface::class, GuzzleScaninaProductRepository::class);
        $this->app->bind(ScaninaUserRepositoryInterface::class, GuzzleScaninaUserRepository::class);
        $this->app->bind(ScaninaRegionRepositoryInterface::class, GuzzleScaninaRegionRepository::class);

        $this->app->bind(SurveyEntityFactoryInterface::class, EloquentSurveyFactoryEntity::class);
        $this->app->bind(RequestedDocumentRepositoryInterface::class, EloquentRequestedDocumentRepository::class);
        $this->app->bind(RequestedDocumentItemRepositoryInterface::class, EloquentRequestedDocumentItemEncryptedRepository::class);
        $this->app->bind(PlafondDisbursementRepositoryInterface::class, PlafondDisbursementEncryptedEloquentRepository::class);
        $this->app->bind(PaymentAccelarationDocumentRepositoryInterface::class, PaymentAccelarationDocumentEncryptedEloquentRepository::class);
        $this->app->bind(ESignRepositoryInterface::class, EloquentESignDocumentEncryptedRepository::class);
        $this->app->bind(AuditHttpLogRepositoryInterface::class, EloquentAuditHttpLogEncryptedRepository::class);
        $this->app->bind(ApiRequestRepositoryInterface::class, EloquentApiRequestLogEncryptedRepository::class);
        $this->app->bind(UserOAuthRepositoryInterface::class, EloquentUserOAuthEncryptedRepository::class);

        // CR2025
        $this->app->bind(PdcHoldRepositoryInterface::class, PdcHoldEloquentRepository::class);
        $this->app->bind(PdcHoldGiroRepositoryInterface::class, PdcHoldGiroEloquentRepository::class);

        // CR 2 2025
        $this->app->bind(SparePartDisbursementRepositoryInterface::class, SparePartDisbursementEloquentRepository::class);
        $this->app->bind(PaymentPreviewRepositoryInterface::class, PaymentPreviewEloquentRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentEloquentRepository::class);
        $this->app->bind(InstallmentRepositoryInterface::class, InstallmentEloquentRepository::class);
        $this->app->bind(WebhookLogRepositoryInterface::class, WebhookLogEloquentRepository::class);

        //SPECIFICATION FACTORY
        $this->app->bind(ProjectSpecificationFactoryInterface::class, EloquentProjectSpecificationFactory::class);
        $this->app->bind(CommoditySpecificationFactoryInterface::class, EloquentCommoditySpecificationFactory::class);
        $this->app->bind(FinancingMethodSpecificationFactoryInterface::class, EloquentFinancingMethodSpecificationFactory::class);
        $this->app->bind(FinancingPrerequisiteSpecificationFactoryInterface::class, EloquentFinancingPrerequisiteSpecificationFactory::class);
        $this->app->bind(FinancingFacilitySpecificationFactoryInterface::class, EloquentFinancingFacilitySpecificationFactory::class);
        $this->app->bind(FinancingApplicationSpecificationFactoryInterface::class, EloquentFinancingApplicationSpecificationFactory::class);
        $this->app->bind(FinancingUnitLocationSubmissionSpecificationFactoryInterface::class, EloquentFinancingUnitLocationSubmissionSpecificationFactory::class);
        $this->app->bind(InvoiceCollectionSubmissionSpecificationFactoryInterface::class, EloquentInvoiceCollectionSubmissionSpecificationFactory::class);
        $this->app->bind(InsuranceClaimSubmissionSpecificationFactoryInterface::class, EloquentInsuranceClaimSubmissionSpecificationFactory::class);
        $this->app->bind(SurveySpecificationFactoryInterface::class, EloquentSurveySpecificationFactory::class);
        $this->app->bind(ESignDocumentSpecificationFactoryInterface::class, EloquentESignDocumentSpecificationFactory::class);
        $this->app->bind(OnBoardingSpecificationFactoryInterface::class, EloquentOnBoardingSpecificationFactory::class);
        $this->app->bind(
            FrequentlyAskQuestionSpecificationFactoryInterface::class,
            EloquentFrequentlyAskQuestionSpecificationFactory::class
        );
        $this->app->bind(
            UserAuthLogSpecificationFactoryInterface::class,
            EloquentUserAuthLogEncryptedSpecificationFactory::class
        );
        $this->app->bind(FinancingCategorySpecificationFactoryInterface::class, EloquentFinancingCategorySpecificationFactory::class);
        $this->app->bind(UserAuthSpecificationFactoryInterface::class, EloquentUserAuthEncryptedSpecificationFactory::class);
        $this->app->bind(RequestedDocumentSpecificationInterface::class, EloquentRequestedDocumentSpecification::class);
        $this->app->bind(ProductCartSpecificationInterface::class, EloquentProductCartSpecification::class);
        $this->app->bind(ScaninaProductSpecificationInterface::class, GuzzleScaninaProductSpecification::class);
        $this->app->bind(ScaninaUserSpecificationInterface::class, GuzzleScaninaUserSpecification::class);
        $this->app->bind(ScaninaRegionSpecificationInterface::class, GuzzleScaninaRegionSpecification::class);
        // CR 2025
        $this->app->bind(PdcHoldSubmissionSpecificationFactoryInterface::class, EloquentPdcHoldSubmissionSpecificationFactory::class);

        // Others
        $this->app->bind('sodiumPasswordBrokerManager', function ($app) {
            return new SodiumPasswordBrokerManager($app);
        });
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
        $this->mergeConfigFrom(__DIR__ . '/../../config/ocr.php', 'ocr');
        $this->mergeConfigFrom(__DIR__ . '/../../config/scanina-web.php', 'scanina-web');
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
