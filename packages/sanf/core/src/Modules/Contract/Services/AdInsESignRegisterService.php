<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\ESignRegisterFormDto;
use Sanf\Core\Modules\Contract\Dto\ResponseESignRegisterDto;
use Sanf\Integration\Modules\AdIns\AdInsESignApiClient;
use Sanf\Integration\Modules\AdIns\DTOs\RegistrationDto;
use Sanf\Integration\Modules\AdIns\Exceptions\AdInsErrorResponseException;

class AdInsESignRegisterService implements ApplicationServiceInterface
{
    public const MALE = 1;

    private AdInsESignApiClient $adInsClient;

    public function __construct(AdInsESignApiClient $adInsClient)
    {
        $this->adInsClient = $adInsClient;
    }

    /**
     * @param ESignRegisterFormDto $dto
     */
    public function execute($dto = null)
    {
        /** @var ESignRegisterFormDto $dto */
        $bodyRequest = new RegistrationDto([
            'fullName' => $dto->fullName,
            'email' => $dto->email,
            'dateOfBirth' => $dto->dob,
            'placeOfBirth' => $dto->pob,
            'gender' => ($dto->gender === self::MALE) ? 'M' : 'F',
            'msisdn' => $dto->msisdn,
            'identityNumber' => $dto->identityNo,
            'address' => $dto->address,
            'province' => $dto->province,
            'city' => $dto->city,
            'district' => $dto->district,
            'subDistrict' => $dto->subDistrict,
            'postalCode' => $dto->postalCode,
            'selfPhoto' => $dto->selfieFile,
            'identityCard' => $dto->identityFile,
            'password' => $dto->password,
        ]);

        $registerResponse = $this->adInsClient->register($bodyRequest);
        if ($registerResponse->status->code !== $this->adInsClient::SUCCESS_CODE) {
            throw new AdInsErrorResponseException("{$registerResponse->status->message}");
        }

        return new ResponseESignRegisterDto([
            'msisdn' => $dto->msisdn,
            'email' => $dto->email,
            'nik' => $dto->identityNo,
            'transactionNo' => implode(',', $registerResponse->trxNo),
        ]);
    }
}
