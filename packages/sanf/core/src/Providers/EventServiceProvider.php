<?php

namespace Sanf\Core\Providers;

use Illuminate\Auth\Events\Login;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Sanf\Api\Modules\Scanina\Events\ProductBuyAddToCartEvent;
use Sanf\Api\Modules\Scanina\Events\ProductRentAddToCartEvent;
use Sanf\Api\Modules\Scanina\Events\ProductServiceAddToCartEvent;
use Sanf\Api\Modules\Scanina\Events\ProductSparePartAddToCartEvent;
use Sanf\Core\Modules\Commodity\Events\CommodityApprovedEvent;
use Sanf\Core\Modules\Commodity\Events\CommodityCreatedEvent;
use Sanf\Core\Modules\Commodity\Events\CommodityRejectedEvent;
use Sanf\Core\Modules\Commodity\Events\CommodityUpdatedEvent;
use Sanf\Core\Modules\Commodity\Listeners\SendEmailRequestApprovalCommodityListener;
use Sanf\Core\Modules\Commodity\Listeners\SendNotificationApprovalCommodityListener;
use Sanf\Core\Modules\Commodity\Listeners\SendNotificationRejectCommodityListener;
use Sanf\Core\Modules\Contract\Events\AdInsDocumentSignCallbackEvent;
use Sanf\Core\Modules\Contract\Events\AdInsRegisterActivationCallbackEvent;
use Sanf\Core\Modules\Contract\Events\AdInsRegisterActivationEvent;
use Sanf\Core\Modules\Contract\Events\ESignAdsInsRegisterMailEvent;
use Sanf\Core\Modules\Contract\Events\ESignAdsInsRegisterNotificationEvent;
use Sanf\Core\Modules\Contract\Events\ESignDocumentDownloadEvent;
use Sanf\Core\Modules\Contract\Events\ESignDocumentSignCompleteNotificationEvent;
use Sanf\Core\Modules\Contract\Events\ESignDocumentSignEvent;
use Sanf\Core\Modules\Contract\Events\FinancingUnitLocationSubmissionAddedEvent;
use Sanf\Core\Modules\Contract\Listeners\CheckStatusESignDocumentListener;
use Sanf\Core\Modules\Contract\Listeners\SendEmailDownloadESignDocumentListener;
use Sanf\Core\Modules\Contract\Listeners\SendEmailESignAdInsRegisterListener;
use Sanf\Core\Modules\Contract\Listeners\SendEmailRequestChangeFinancingUnitLocationListener;
use Sanf\Core\Modules\Contract\Listeners\SendNotificationESignAdInsRegisterListener;
use Sanf\Core\Modules\Contract\Listeners\SendNotificationESignDocumentSignCompleteListener;
use Sanf\Core\Modules\Contract\Listeners\UpdateAdInsUserStatusByCallbackListener;
use Sanf\Core\Modules\Contract\Listeners\UpdateAdInsUserStatusListener;
use Sanf\Core\Modules\Contract\Listeners\UpdateESignDocumentStatusByCallbackListener;
use Sanf\Core\Modules\Financing\Events\FinancingApplicationCreatedEvent;
use Sanf\Core\Modules\Financing\Listeners\SendEmailNewFinancingApplicationListener;
use Sanf\Core\Modules\Insurance\Events\InsuranceClaimSubmissionAddedEvent;
use Sanf\Core\Modules\Insurance\Listeners\SendEmailNewInsuranceClaimSubmissionListener;
use Sanf\Core\Modules\Invoice\Events\InvoiceCollectionSubmissionAddedEvent;
use Sanf\Core\Modules\Invoice\Listeners\SendEmailNewInvoiceCollectionSubmissionListener;
use Sanf\Core\Modules\Notification\Events\NotifiedUserByExternalEvent;
use Sanf\Core\Modules\Notification\Listeners\SendPushNotificationByExternalListener;
use Sanf\Core\Modules\Payment\Events\PaymentCompletedEvent;
use Sanf\Core\Modules\Payment\Events\PaymentCreatedEvent;
use Sanf\Core\Modules\Payment\Events\PaymentExpiredEvent;
use Sanf\Core\Modules\Payment\Listeners\SendNotificationPaymentCompletedListener;
use Sanf\Core\Modules\Payment\Listeners\SendNotificationPaymentCreatedListener;
use Sanf\Core\Modules\Payment\Listeners\SendNotificationPaymentExpiredListener;
use Sanf\Core\Modules\PdcHold\Events\PdcHoldMultiContractSubmittedEvent;
use Sanf\Core\Modules\PdcHold\Events\PdcHoldMultiGiroSubmittedEvent;
use Sanf\Core\Modules\PdcHold\Events\PdcHoldSubmissionUpdateByCoreNotificationEvent;
use Sanf\Core\Modules\PdcHold\Listeners\SendEmailSubmitPdcHoldMultiContractListener;
use Sanf\Core\Modules\PdcHold\Listeners\SendEmailSubmitPdcHoldMultiGiroListener;
use Sanf\Core\Modules\PdcHold\Listeners\SendNotificationPdcHoldSubmissionUpdateByCoreListener;
use Sanf\Core\Modules\Plafond\Events\PlafondDisbursementSubmittedMailEvent;
use Sanf\Core\Modules\Plafond\Events\PlafondDisbursementSubmittedNotificationEvent;
use Sanf\Core\Modules\Plafond\Events\PlafondDisbursementUpdateByCoreNotificationEvent;
use Sanf\Core\Modules\Plafond\Events\PlafondIncreaseRequestedEvent;
use Sanf\Core\Modules\Plafond\Events\PlafondRequestedEvent;
use Sanf\Core\Modules\Plafond\Listeners\SendEmailPlafondDisbursementSubmittedListener;
use Sanf\Core\Modules\Plafond\Listeners\SendEmailRequestIncreasePlafondListener;
use Sanf\Core\Modules\Plafond\Listeners\SendEmailRequestNewPlafondListener;
use Sanf\Core\Modules\Plafond\Listeners\SendNotificationPlafondDisbursementSubmittedListener;
use Sanf\Core\Modules\Plafond\Listeners\SendNotificationPlafondDisbursementUpdateByCoreListener;
use Sanf\Core\Modules\Prepayment\Events\PrepaymentSubmissionAddedEvent;
use Sanf\Core\Modules\Prepayment\Listeners\SendEmailNewPrepaymentSubmissionListener;
use Sanf\Core\Modules\Project\Events\ProjectApprovedEvent;
use Sanf\Core\Modules\Project\Events\ProjectCreatedEvent;
use Sanf\Core\Modules\Project\Events\ProjectRejectedEvent;
use Sanf\Core\Modules\Project\Events\ProjectUpdatedEvent;
use Sanf\Core\Modules\Project\Listeners\SendEmailRequestApprovalProjectListener;
use Sanf\Core\Modules\Project\Listeners\SendNotificationApprovalProjectListener;
use Sanf\Core\Modules\Project\Listeners\SendNotificationRejectProjectListener;
use Sanf\Core\Modules\Scanina\Listeners\SendEmailProductBuyAddToCartListener;
use Sanf\Core\Modules\Scanina\Listeners\SendEmailProductRentAddToCartListener;
use Sanf\Core\Modules\Scanina\Listeners\SendEmailProductServiceAddToCartListener;
use Sanf\Core\Modules\Scanina\Listeners\SendEmailProductSparePartAddToCartListener;
use Sanf\Core\Modules\User\Listeners\LogSuccessfulLoginListener;
use Sanf\Dashboard\Modules\User\Events\AccountBindingCreatedByCoreNotificationEvent;
use Sanf\Dashboard\Modules\User\Listeners\SendEmailActivationAccountBindingByCoreListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            LogSuccessfulLoginListener::class,
        ],
        ProjectCreatedEvent::class => [
            SendEmailRequestApprovalProjectListener::class,
        ],
        CommodityCreatedEvent::class => [
            SendEmailRequestApprovalCommodityListener::class,
        ],
        ProjectUpdatedEvent::class => [
            SendEmailRequestApprovalProjectListener::class,
        ],
        CommodityUpdatedEvent::class => [
            SendEmailRequestApprovalCommodityListener::class,
        ],
        PlafondRequestedEvent::class => [
            SendEmailRequestNewPlafondListener::class,
        ],
        PlafondIncreaseRequestedEvent::class => [
            SendEmailRequestIncreasePlafondListener::class,
        ],
        FinancingApplicationCreatedEvent::class => [
            SendEmailNewFinancingApplicationListener::class,
        ],
        PrepaymentSubmissionAddedEvent::class => [
            SendEmailNewPrepaymentSubmissionListener::class,
        ],
        FinancingUnitLocationSubmissionAddedEvent::class => [
            SendEmailRequestChangeFinancingUnitLocationListener::class,
        ],
        InvoiceCollectionSubmissionAddedEvent::class => [
            SendEmailNewInvoiceCollectionSubmissionListener::class,
        ],
        InsuranceClaimSubmissionAddedEvent::class => [
            SendEmailNewInsuranceClaimSubmissionListener::class,
        ],
        ESignDocumentDownloadEvent::class => [
            SendEmailDownloadESignDocumentListener::class,
        ],
        CommodityApprovedEvent::class => [
            SendNotificationApprovalCommodityListener::class,
        ],
        CommodityRejectedEvent::class => [
            SendNotificationRejectCommodityListener::class,
        ],
        ProjectApprovedEvent::class => [
            SendNotificationApprovalProjectListener::class,
        ],
        ProjectRejectedEvent::class => [
            SendNotificationRejectProjectListener::class,
        ],
        NotifiedUserByExternalEvent::class => [
            SendPushNotificationByExternalListener::class,
        ],
        ProductBuyAddToCartEvent::class => [
            SendEmailProductBuyAddToCartListener::class,
        ],
        ProductRentAddToCartEvent::class => [
            SendEmailProductRentAddToCartListener::class,
        ],
        ProductSparePartAddToCartEvent::class => [
            SendEmailProductSparePartAddToCartListener::class,
        ],
        ProductServiceAddToCartEvent::class => [
            SendEmailProductServiceAddToCartListener::class,
        ],
        PlafondDisbursementSubmittedMailEvent::class => [
            SendEmailPlafondDisbursementSubmittedListener::class,
        ],
        PlafondDisbursementSubmittedNotificationEvent::class => [
            SendNotificationPlafondDisbursementSubmittedListener::class,
        ],
        PlafondDisbursementUpdateByCoreNotificationEvent::class => [
            SendNotificationPlafondDisbursementUpdateByCoreListener::class,
        ],
        AccountBindingCreatedByCoreNotificationEvent::class => [
            SendEmailActivationAccountBindingByCoreListener::class,
        ],
        ESignAdsInsRegisterMailEvent::class => [
            SendEmailESignAdInsRegisterListener::class,
        ],
        ESignAdsInsRegisterNotificationEvent::class => [
            SendNotificationESignAdInsRegisterListener::class,
        ],
        ESignDocumentSignCompleteNotificationEvent::class => [
            SendNotificationESignDocumentSignCompleteListener::class,
        ],
        AdInsRegisterActivationCallbackEvent::class => [
            UpdateAdInsUserStatusByCallbackListener::class,
        ],
        AdInsDocumentSignCallbackEvent::class => [
            UpdateESignDocumentStatusByCallbackListener::class,
        ],
        AdInsRegisterActivationEvent::class => [
            UpdateAdInsUserStatusListener::class,
        ],
        ESignDocumentSignEvent::class => [
            CheckStatusESignDocumentListener::class,
        ],
        PdcHoldMultiGiroSubmittedEvent::class => [
            SendEmailSubmitPdcHoldMultiGiroListener::class,
        ],
        PdcHoldMultiContractSubmittedEvent::class => [
            SendEmailSubmitPdcHoldMultiContractListener::class,
        ],
        PdcHoldSubmissionUpdateByCoreNotificationEvent::class => [
            SendNotificationPdcHoldSubmissionUpdateByCoreListener::class,
        ],
        PaymentExpiredEvent::class => [
            SendNotificationPaymentExpiredListener::class,
        ],
        PaymentCreatedEvent::class => [
            SendNotificationPaymentCreatedListener::class,
        ],
        PaymentCompletedEvent::class => [
            SendNotificationPaymentCompletedListener::class,
        ],
    ];
}
