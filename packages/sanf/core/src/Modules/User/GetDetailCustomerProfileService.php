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
        $response = $this->internalApiClient->findCustomerByEmail($dto->email);

        return collect($response['data'])
            ->map(function ($item){
                return (object)[
                    "xid" => $item['CUST_ID_SANF'],
                    "type_id" => $item['ID_IDENTITY'],
                    "type_name" => $item['DESC_IDENTITY'],
                    "title" => $item['COMPANY_TYPE'],
                    "full_name" => $item['IDENTITY_NAME'],
                    "pic_name" => $item['PIC_NAME'],
                    "identity_number" => $item['KTP'],
                    "npwp" => $item['NPWP'],
                    "email" => $item['EMAIL_ADDR'],
                    "landline_number" => $item['NO_TELP'],
                    "phone_number" => $item['NO_HP'],
                    "gender" => $item['GENDER'],
                    "birthdate" => Carbon::createFromFormat('Y/m/d', $item['TGL_LAHIR'])->format('Y-m-d'),
                    "country_id" => $item['ID_NEGARA'],
                    "country_name" => $item['NEGARA'],
                    "province_id" => $item['ID_PROVINSI'],
                    "province_name" => $item['PROVINSI'],
                    "city_id" => $item['ID_KOTA'],
                    "city_name" => $item['KOTA'],
                    "district_name" => $item['KECAMATAN'],
                    "subdistrict_name" => $item['KELURAHAN'],
                    "postcode" => $item['KODEPOS'],
                    "address" => $item['ALAMAT'],
                    "business_since" => $item['LAMA_USAHA'],
                    "is_pic" => $item['PIC']
                ];
            });
    }
}
