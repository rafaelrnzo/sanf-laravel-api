<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Modules\AdIns\AdInsESignApiClient;
use Sanf\Integration\Modules\AdIns\DTOs\DocumentDto;
use Sanf\Integration\Modules\AdIns\Exceptions\AdInsErrorResponseException;

class AdInsESignDocumentSignCheckService implements ApplicationServiceInterface
{
    protected AdInsESignApiClient $adInsClient;

    public function __construct(AdInsESignApiClient $adInsClient)
    {
        $this->adInsClient = $adInsClient;
    }

    /**
     * @throws AdInsErrorResponseException
     */
    public function execute($dto = null)
    {
        /**
         * @var RequestESignDocumentSignDto $dto
         */
        $bodyRequest = new DocumentDto(
            [
                'referenceNo' => $dto->referenceNo,
            ]
        );

        $signResponse = $this->adInsClient->signDocumentCheck($bodyRequest);
        if ($signResponse->status->code !== $this->adInsClient::SUCCESS_CODE) {
            throw new AdInsErrorResponseException("{$signResponse->status->message}");
        }

        $signedDocument = null;
        foreach ($signResponse->statusSigning ?? [] as $document) {
            if ($document->documentId !== $dto->documentId) {
                continue;
            }
            $signedDocument = $document;
        }

        return (object) [
            'referenceNo' => $dto->referenceNo,
            'statusSigning' => $signedDocument,
        ];
    }
}
