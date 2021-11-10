<?php


namespace Sanf\Core\Modules\User\Services;


use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Integration\InternalApiClient;

class UpdatePersonalProfileService implements ApplicationServiceInterface
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

    public function execute($dto = null)
    {
        $user = $this->repository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $profile = $this->internalApiClient->findCustomerById($dto->customerId);
        if($profile['data'][0]['ID_IDENTITY'] !== ProfileType::PERSONAL){
            throw new UserNotFoundException('Missmatch Type');
        }
        $this->internalApiClient->updateCustomer([
            "cust_id" => $dto->customerId,
            "ktp" => $dto->identityNumber,
            "cust_type" => ProfileType::PERSONAL,
            "notelp" => $dto->landlineNumber,
            "nohp" => $dto->phoneNumber,
            "gender" => $dto->gender,
            "tgl_lahir" => Carbon::make($dto->birthdate)->format('Y/m/d'),
            "idprov" => $dto->provinceId,
            "prov" => $dto->provinceName,
            "idkota" => $dto->cityId,
            "kota" => $dto->cityName,
            "kecamatan" => $dto->districtName,
            "kelurahan" => $dto->subdistrictName,
            "kodepos" => $dto->postcode,
            "alamat" => $dto->address,
            "lama_usaha" => $dto->businessSince,
        ]);
    }
}
