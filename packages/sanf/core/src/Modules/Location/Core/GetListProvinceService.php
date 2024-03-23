<?php

namespace Sanf\Core\Modules\Location\Core;

use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetListProvinceService
{
    protected SanfCoreApiClient $client;

    public function __construct(SanfCoreApiClient $client)
    {
        $this->client = $client;
    }

    public function execute()
    {
        $response = $this->client->getProvinces();

        return collect($response['data'])
            ->map(function ($item) {
                return (object) [
                    'country_id' => $item['COUNTRY_ID'] ?? '',
                    'province_id' => $item['PROVINSI'] ?? '',
                    'name' => ucwords(strtolower($item['DESCRIPTION'] ?? '')),
                ];
            });
    }
}
