<?php


namespace Sanf\Core\Modules\Location\Core;


use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetListCityService
{

    protected SanfCoreApiClient $client;

    public function __construct(SanfCoreApiClient $client)
    {
        $this->client = $client;
    }

    public function execute($dto)
    {
        $response = $this->client->getCitiesByProviceId($dto->province_id);

        return collect($response['data'])
            ->map(function ($item) {
                return (object)[
                    "country_id" => $item['COUNTRY_ID'] ?? '',
                    "province_id" => $item['PROVINSI_ID'] ?? '',
                    "city_id" => $item['CITY_ID'] ?? '',
                    "city_name" => ucwords(strtolower($item['DESCRIPTION'] ?? '')),
                ];
            });
    }
}
