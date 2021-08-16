<?php


namespace Sanf\Core\Modules\User;


use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\InternalApiClient;

class GetDetailCustomerProfileService implements ApplicationServiceInterface
{
    protected $internalApiClient;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(InternalApiClient $internalApiClient) //TODO REPOSITORY
    {
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto)
    {
        $response = $this->internalApiClient->findCustomerById($dto->customerId);

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
