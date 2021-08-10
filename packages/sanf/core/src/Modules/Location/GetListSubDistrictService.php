<?php


namespace Sanf\Core\Modules\Location;


use Sanf\Integration\InternalApiClient;

class GetListSubDistrictService
{

    protected InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {
        $this->client = $client;
    }

    public function execute($dto)
    {
        $response = $this->client->getSubDistrict($dto->province_id, $dto->city_id, strtoupper($dto->district_name));

        return collect($response['data'])
            ->map(function ($item) {
                return (object)[
                    "country_id" => $item['COUNTRY_ID'] ?? '',
                    "province_id" => $item['PROVINSI_ID'] ?? '',
                    "city_id" => $item['CITY_ID'] ?? '',
                    "district_name" => ucwords(strtolower($item['DISTRICT'] ?? '')),
                    "sub_district_name" => ucwords(strtolower($item['SUBDISTRICT'] ?? '')),
                    "postcode" => $item['POSTCODE'] ?? '',
                ];
            });
    }
}