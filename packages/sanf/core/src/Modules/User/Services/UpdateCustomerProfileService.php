<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\InternalApiClient;

class UpdateCustomerProfileService implements ApplicationServiceInterface
{
    protected $repository;
    protected $internalApiClient;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(AuthModel $repository, InternalApiClient $internalApiClient) //TODO REPOSITORY
    {
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto)
    {
        $user = $this->repository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $this->internalApiClient->updateCustomer([
            "cust_id" => "",
            "cust_type" => "",
            "cust_title" => "",
            "nama" => "",
            "picname" => "",
            "ktp" => "",
            "npwp" => "",
            "email" => "",
            "notelp" => "",
            "nohp" => "",
            "gender" => "",
            "tgl_lahir" => "",
            "idprov" => "",
            "prov" => "",
            "idkota" => "",
            "kota" => "",
            "kecamatan" => "",
            "kelurahan" => "",
            "kodepos" => ""
        ]);
    }
}
