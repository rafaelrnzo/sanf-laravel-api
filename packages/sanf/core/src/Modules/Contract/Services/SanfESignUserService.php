<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\GetESignUserResponseDto;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\User\Repositories\RestProfileRepository;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class SanfESignUserService implements ApplicationServiceInterface
{
    protected RestProfileRepository $sanfProfileRepository;
    protected SanfCoreApiClient $sanfCoreClient;

    public function __construct(RestProfileRepository $sanfProfileRepository, SanfCoreApiClient $sanfCoreClient)
    {
        $this->sanfProfileRepository = $sanfProfileRepository;
        $this->sanfCoreClient = $sanfCoreClient;
    }

    public function execute($dto = null)
    {
        $userSanfResponse = $this->sanfProfileRepository->findById($dto->profileXid);
        if (is_null($userSanfResponse)) {
            throw new UserNotFoundException();
        }

        $eSignSanfUserResponse = $this->sanfCoreClient->getAvailableESignUser($userSanfResponse->getEmail());

        return array_map(function ($item) use ($userSanfResponse) {
            return [
                'email' => isset($item['EMAIL']) ? $item['EMAIL'] : $userSanfResponse->getEmail(),
                'msisdn' => isset($item['MOBILE']) ? $item['MOBILE'] : $userSanfResponse->getPhoneNumber(),
                'nik' => isset($item['NIK']) ? $item['NIK'] : $userSanfResponse->getIdentityNumber(),
                'fullName' => isset($item['NAME']) ? $item['NAME'] : $userSanfResponse->getFullName(),
                'dob' => isset($item['DOB']) ? $item['DOB'] : null,
                'pob' => isset($item['POB']) ? $item['POB'] : null,
                'gender' => isset($item['GENDER']) ? (int) $item['GENDER'] : $userSanfResponse->getGender(),
                'address' => isset($item['ADDRESS']) ? $item['ADDRESS'] : $userSanfResponse->getAddress(),
                'postalCode' => isset($item['ZIP_CODE']) ? (int) $item['ZIP_CODE'] : $userSanfResponse->getPostcode(),
                'statusId' => ESignRegistrationStatusEnum::AVAILABLE,
            ];
        }, $eSignSanfUserResponse['data'])[0];

        $eSignUser = $eSignSanfUserResponse;

        return new GetESignUserResponseDto($eSignUser);
    }
}
