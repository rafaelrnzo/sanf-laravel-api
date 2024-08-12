<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\RequestESignDocumentSignDto;
use Sanf\Integration\Modules\AdIns\AdInsESignApiClient;
use Sanf\Integration\Modules\AdIns\DTOs\DocumentDto;
use Sanf\Integration\Modules\AdIns\Exceptions\AdInsErrorResponseException;

class AdInsESignSignDocumentService implements ApplicationServiceInterface
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
            'documentsId' => [$dto->documentId],
            'email' => $dto->email,
            'msisdn' => $dto->msisdn,
            'password' => $dto->password,
            'ip' => $dto->ipAddress,
            'browser' => $dto->userAgent,
            'otp' => $dto->otp,

        ]);

        $signResponse = $this->adInsClient->signDocument($bodyRequest);
        if ($signResponse->status->code !== $this->adInsClient::SUCCESS_CODE) {
            throw new AdInsErrorResponseException("{$signResponse->status->message}");
        }

        return (object) [
            'msisdn' => $dto->msisdn,
            'email' => $dto->email,
            'transactionNo' => $signResponse->trxNo ?? null,
        ];
    }
}
