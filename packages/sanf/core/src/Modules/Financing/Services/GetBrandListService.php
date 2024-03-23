<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetBrandListService implements ApplicationServiceInterface
{
    private SanfCoreApiClient $client;

    public function __construct(SanfCoreApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throw Exception
     */
    public function execute($dto = null)
    {
        $data = $this->client->getBrands();

        return (object) [
            'data' => collect($data['data'])
                ->map(function ($item) {
                    return (object) [
                        'brand_id' => $item['BRAND_ID'] ?? null,
                        'brand_name' => $item['BRAND_NAME'] ?? null,
                    ];
                }),
            'paginate' => (object) [
                'total' => (int) ($data['total'] ?? 0),
                'count' => (int) ($data['count'] ?? 0),
                'skip' => null,
                'limit' => null,
                'sort_by' => null,
            ],
        ];
    }
}
