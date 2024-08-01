<?php

namespace Sanf\Core\Modules\Contract\Services;

use DateTime;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\RequestESignDocumentOTPDto;
use Sanf\Core\Modules\Contract\Dto\ResponseESignDocumentOTPDto;
use Sanf\Integration\Modules\AdIns\AdInsESignApiClient;
use Sanf\Integration\Modules\AdIns\DTOs\OneTimePasswordDto;
use Sanf\Integration\Modules\AdIns\Exceptions\AdInsErrorResponseException;

class AdInsESignDocumentOTPService implements ApplicationServiceInterface
{
    private AdInsESignApiClient $adInsClient;

    public function __construct(AdInsESignApiClient $adInsClient)
    {
        $this->adInsClient = $adInsClient;
    }

    /**
     * @param RequestESignDocumentOTPDto $dto
     * @return ResponseESignDocumentOTPDto
     * @throws AdInsErrorResponseException
     */
    public function execute($dto = null)
    {
        $msisdn = $this->parseMsisdnWithZeroFormat($dto->msisdn);
        $bodyRequest = new OneTimePasswordDto([
            'msisdn' => $msisdn,
            'email' => $dto->email,
            'referenceNo' => $dto->referenceNo,
        ]);

        $otpResponse = $this->adInsClient->requestOTP($bodyRequest);
        if ($otpResponse->status->code !== $this->adInsClient::SUCCESS_CODE) {
            throw new AdInsErrorResponseException("{$otpResponse->status->message}");
        }

        return new ResponseESignDocumentOTPDto([
            'msisdn' => $msisdn,
            'email' => $dto->email,
            'expiredAt' => (new DateTime())->modify('+1 minute'),
            'referenceNo' => $dto->referenceNo,
            'transactionNo' => $otpResponse->trxNo,
        ]);
    }

    private function parseMsisdnWithZeroFormat(string $msisdn): string
    {
        $trimValue = trim($msisdn);

        if (strpos($trimValue, '+62') === 0) {
            $msisdn = '0' . substr($trimValue, 3);
        } elseif (strpos($trimValue, '62') === 0) {
            $msisdn = '0' . substr($trimValue, 2);
        }

        return $msisdn;
    }
}
