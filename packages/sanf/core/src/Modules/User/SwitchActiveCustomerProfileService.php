<?php


namespace Sanf\Core\Modules\User;


use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\InternalApiClient;

class SwitchActiveCustomerProfileService implements ApplicationServiceInterface
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
        $response = $this->internalApiClient->findCustomerById($dto->customerId);

        //TODO REPO
        $user->xid = $response['data'][0]['CUST_ID_SANF'];
        $user->profile_type = $response['data'][0]['ID_IDENTITY'];
        $user->save();

        return collect($response['data'])
            ->map(function ($item){
                return (object)[
                    "xid" => $item['CUST_ID_SANF'],
                    "typeId" => $item['ID_IDENTITY'],
                    "typeName" => $item['DESC_IDENTITY'],
                    "title" => $item['COMPANY_TYPE'],
                    "fullName" => $item['IDENTITY_NAME'],
                    "picName" => $item['PIC_NAME'],
                    "identityNumber" => $item['KTP'],
                    "npwp" => $item['NPWP'],
                    "email" => $item['EMAIL_ADDR'],
                    "landlineNumber" => $item['NO_TELP'],
                    "phoneNumber" => $item['NO_HP'],
                    "gender" => $item['GENDER'],
                    "birthdate" => Carbon::make($item['TGL_LAHIR']),
                    "countryId" => $item['ID_NEGARA'],
                    "countryName" => $item['NEGARA'],
                    "provinceId" => $item['ID_PROVINSI'],
                    "provinceName" => $item['PROVINSI'],
                    "cityId" => $item['ID_KOTA'],
                    "cityName" => $item['KOTA'],
                    "districtName" => $item['KECAMATAN'],
                    "subdistrictName" => $item['KELURAHAN'],
                    "postcode" => $item['KODEPOS'],
                    "address" => $item['ALAMAT'],
                    "businessSince" => $item['LAMA_USAHA'],
                    "isPic" => $item['PIC']
                ];
            });
    }
}
