<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentEncryptedRepository;

class ESignDocumentDownloadService implements ApplicationServiceInterface
{
    protected ESignDocumentSignCheckService $eSignDocumentSignCheckService;
    protected AdInsESignDownloadDocumentService $adInsDownloadDocumentService;
    protected EloquentESignDocumentEncryptedRepository $eSignRepository;

    public function __construct(
        ESignDocumentSignCheckService $eSignDocumentSignCheckService,
        AdInsESignDownloadDocumentService $adInsDownloadDocumentService,
        EloquentESignDocumentEncryptedRepository $eSignRepository
    ) {
        $this->eSignDocumentSignCheckService = $eSignDocumentSignCheckService;
        $this->adInsDownloadDocumentService = $adInsDownloadDocumentService;
        $this->eSignRepository = $eSignRepository;
    }

    public function execute($dto = null)
    {
        $eSignDocument = $this->eSignRepository->findDocumentByDocId($dto->documentId);
        if (is_null($eSignDocument) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $downloadResult = $this->adInsDownloadDocumentService->execute($dto);
        if (is_null($downloadResult->documentFileBase64) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $documentBinary = base64_decode($downloadResult->documentFileBase64);

        if ($eSignDocument->status_id !== ESignContractStatusEnum::COMPLETED) {
            $this->eSignDocumentSignCheckService->execute($dto);
        }

        return $documentBinary;
    }
}
