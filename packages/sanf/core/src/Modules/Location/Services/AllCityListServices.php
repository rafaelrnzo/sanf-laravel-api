<?php

namespace Sanf\Core\Modules\Location\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Location\ListCityDto;
use Sanf\Integration\InternalApiClient;

class AllCityListServices implements ApplicationServiceInterface
{

    protected InternalApiClient $internalApiClient;

    public function __construct(InternalApiClient $internalApiClient)
    {
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param ListCityDto $dto
     * @return object
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $response = $this->internalApiClient->getCities($dto);
        $data = collect($response['data'])->map(function ($item) {
            return (object)[
                'id' => $item['CITY_ID'] ?? null,
                'name' => $item['DESCRIPTION'] ?? null,
            ];
        });

        switch ($dto->sort_by) {
            case 'name_desc':
                $sort = $data->sortByDesc('name');
                break;
            case 'name_asc':
            default:
                $sort = $data->sortBy('name');
                break;
        }

        return (object)[
            'data' => $sort,
            'paginate' => (object)[
                'total' => $response['total'] ?? $response['count'],
                'count' => $response['count'] ?? 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];
    }
}