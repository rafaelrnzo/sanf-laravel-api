<?php

namespace Sanf\Core\Modules\Finance\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\InternalApiClient;

class GetBrandListService implements ApplicationServiceInterface
{

    private InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throw Exception
     */
    public function execute($dto = null)
    {
        $data = $this->client->getBrands();

        return (object)[
            'data' => collect($data['data'])
                ->map(function ($item) {
                    return (object)[
                        "brand_id" => $item['BRAND_ID'] ?? null,
                        "brand_name" => $item['BRAND_NAME'] ?? null,
                    ];
                }),
            'paginate' => (object)[
                'total' => (int)($data['total'] ?? 0),
                'count' => (int)($data['count'] ?? 0),
                'skip' => null,
                'limit' => null,
                'sort_by' => null,
            ],
        ];
    }
}