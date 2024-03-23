<?php

namespace Sanf\Core\Modules\Location\Core;

use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetListDistrictService
{
    protected SanfCoreApiClient $client;

    public function __construct(SanfCoreApiClient $client)
    {
        $this->client = $client;
    }

    public function execute($dto)
    {
        $response = $this->client->getDistrict($dto->province_id, $dto->city_id);

        return collect($response['data'])
            ->map(function ($item) {
                return (object) [
                    'country_id' => $item['COUNTRY_ID'] ?? '',
                    'province_id' => $item['PROVINSI_ID'] ?? '',
                    'city_id' => $item['CITY_ID'] ?? '',
                    'district_name' => ucwords(strtolower($item['DESCRIPTION'] ?? '')),
                ];
            });
    }
}
