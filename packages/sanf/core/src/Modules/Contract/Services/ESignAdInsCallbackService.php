<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Exceptions\UndefinedSwitchCaseException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\AdInsCallbackTypeEnum;
use Sanf\Core\Modules\Contract\Events\AdInsDocumentSignCallbackEvent;
use Sanf\Core\Modules\Contract\Events\AdInsRegisterActivationCallbackEvent;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;
use Sanf\Core\Modules\Contract\Specifications\ESignDocumentSpecificationFactoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class ESignAdInsCallbackService implements ApplicationServiceInterface
{
    protected EloquentESignDocumentRepository $eSignDocumentRepository;
    protected ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory;
    protected SanfCoreApiClient $sanfCoreClient;

    public function __construct(
        EloquentESignDocumentRepository $eSignDocumentRepository,
        ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory,
        SanfCoreApiClient $sanfCoreClient
    ) {
        $this->eSignDocumentRepository = $eSignDocumentRepository;
        $this->eSignDocumentSpecificationFactory = $eSignDocumentSpecificationFactory;
        $this->sanfCoreClient = $sanfCoreClient;
    }

    public function execute($dto = null)
    {
        switch ($dto->callbackType) {
            case AdInsCallbackTypeEnum::ACTIVATION_COMPLETE:
                event(new AdInsRegisterActivationCallbackEvent($dto));
                break;
            case AdInsCallbackTypeEnum::SIGNING_COMPLETE:
            case AdInsCallbackTypeEnum::DOCUMENT_SIGN_COMPLETE:
            case AdInsCallbackTypeEnum::ALL_DOCUMENT_SIGN_COMPLETE:
                event(new AdInsDocumentSignCallbackEvent($dto));
                break;
            default:
                throw new UndefinedSwitchCaseException();
                break;
        }

        return true;
    }
}
