<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\RequestESignRegisterFormDto;
use Sanf\Core\Modules\Contract\Dto\ResponseESignRegisterDto;
use Sanf\Integration\Modules\AdIns\AdInsESignApiClient;
use Sanf\Integration\Modules\AdIns\DTOs\RegistrationDto;
use Sanf\Integration\Modules\AdIns\Exceptions\AdInsErrorResponseException;

class AdInsESignRegisterCheckService implements ApplicationServiceInterface
{
    public const UNREGISTERED = 0;
    public const INACTIVE = 1;
    public const ACTIVE = 2;

    public const CERTIFICATE_STATUS_ACTIVE = '1';
    public const CERTIFICATE_STATUS_EXPIRED = '0';

    private AdInsESignApiClient $adInsClient;

    public function __construct(AdInsESignApiClient $adInsClient)
    {
        $this->adInsClient = $adInsClient;
    }

    /**
     * @param RequestESignRegisterFormDto $dto
     */
    public function execute($dto = null)
    {
        /** @var RequestESignRegisterFormDto $dto */
        $bodyRequest = new RegistrationDto([
            'email' => $dto->email,
            'msisdn' => $dto->msisdn,
            'identityNumber' => $dto->identityNo,
        ]);

        $registerResponse = $this->adInsClient->registerCheckByEmail($bodyRequest);
        if ($registerResponse->status->code !== $this->adInsClient::SUCCESS_CODE) {
            throw new AdInsErrorResponseException("{$registerResponse->status->message}");
        }

        return new ResponseESignRegisterDto([
            'msisdn' => $dto->msisdn,
            'email' => $dto->email,
            'nik' => $dto->identityNo,
            'status' => $registerResponse->registrationData ?? null,
        ]);
    }
}
