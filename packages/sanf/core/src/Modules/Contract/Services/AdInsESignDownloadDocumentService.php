<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\RequestESignDocumentSignDto;
use Sanf\Integration\Modules\AdIns\AdInsESignApiClient;
use Sanf\Integration\Modules\AdIns\DTOs\DocumentDto;
use Sanf\Integration\Modules\AdIns\Exceptions\AdInsErrorResponseException;

class AdInsESignDownloadDocumentService implements ApplicationServiceInterface
{
    private AdInsESignApiClient $adInsClient;

    public function __construct(AdInsESignApiClient $adInsClient)
    {
        $this->adInsClient = $adInsClient;
    }

    /**
     * @param RequestESignDocumentSignDto $dto
     * @throws AdInsErrorResponseException
     */
    public function execute($dto = null)
    {
        /** @var RequestESignDocumentSignDto $dto */
        $bodyRequest = new DocumentDto([
            'documentId' => $dto->documentId,
        ]);

        $signResponse = $this->adInsClient->signDocumentDownload($bodyRequest);
        if ($signResponse->status->code !== $this->adInsClient::SUCCESS_CODE) {
            throw new AdInsErrorResponseException("{$signResponse->status->message}");
        }

        return (object) [
            'documentId' => $dto->documentId,
            'documentFileBase64' => $signResponse->pdfBase64 ?? null,
        ];
    }
}
