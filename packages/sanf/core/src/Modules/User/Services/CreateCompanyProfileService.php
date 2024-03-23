<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class CreateCompanyProfileService implements ApplicationServiceInterface
{
    protected $repository;
    protected $internalApiClient;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(AuthModel $repository, SanfCoreApiClient $internalApiClient) //TODO REPOSITORY
    {
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto = null)
    {
        $user = $this->repository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $this->internalApiClient->createCompany([
            'cust_accnt' => $dto->customerId,
            'cust_title' => $dto->title,
            'nama' => $dto->fullName,
            'npwp' => $dto->npwp,
            'no_telp' => $dto->landlineNumber,
            'pic_name' => $dto->picName,
            'no_hp' => $dto->phoneNumber,
            'email' => $dto->email,
        ]);
    }
}
