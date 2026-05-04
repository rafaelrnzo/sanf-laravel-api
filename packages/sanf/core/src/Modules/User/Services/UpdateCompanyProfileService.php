<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Exceptions\ForbiddenException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class UpdateCompanyProfileService implements ApplicationServiceInterface
{
    protected $repository;
    protected $internalApiClient;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(UserRepositoryInterface $repository, SanfCoreApiClient $internalApiClient) //TODO REPOSITORY
    {
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto = null)
    {
        $user = $this->repository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $profile = $this->internalApiClient->findCustomerById($dto->customerId);
        if ($profile['data'][0]['EMAIL_ADDR'] !== $user->username) {
            throw new ForbiddenException('Missmatch User Access');
        }
        if ($profile['data'][0]['ID_IDENTITY'] !== ProfileType::COMPANY) {
            throw new UserNotFoundException('Missmatch Type');
        }
        $this->internalApiClient->updateCustomer([
            'cust_id' => $dto->customerId,
            'cust_type' => ProfileType::COMPANY,
            'notelp' => $dto->landlineNumber,
            'nohp' => $dto->phoneNumber,
            'idprov' => $dto->provinceId,
            'prov' => $dto->provinceName,
            'idkota' => $dto->cityId,
            'kota' => $dto->cityName,
            'kecamatan' => $dto->districtName,
            'kelurahan' => $dto->subdistrictName,
            'kodepos' => $dto->postcode,
            'alamat' => $dto->address,
            'lama_usaha' => $dto->businessSince,
            'picname' => $dto->picName ?? $profile['data'][0]['PIC_NAME'],
        ]);
    }
}
