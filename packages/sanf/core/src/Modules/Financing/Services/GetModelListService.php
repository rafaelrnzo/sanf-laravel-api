<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\InternalApiClient;

class GetModelListService implements ApplicationServiceInterface
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
        $data = $this->client->getModels($dto->brand_id, $dto->type_id);

        return (object)[
            'data' => collect($data['data'])
                ->map(function ($item) {
                    return (object)[
                        "brand_id" => $item['BRAND_ID'] ?? null,
                        "type_id" => $item['TYPE_ID'] ?? null,
                        "model_id" => $item['MODEL_ID'] ?? null,
                        "model_name" => $item['MODEL_NAME'] ?? null,
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