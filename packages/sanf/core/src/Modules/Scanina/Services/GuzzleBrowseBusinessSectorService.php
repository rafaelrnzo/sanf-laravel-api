<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseBusinessSectorResponseDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaRegionRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaRegionSpecificationInterface;

class GuzzleBrowseBusinessSectorService implements ApplicationServiceInterface
{

    private ScaninaRegionRepositoryInterface $repository;
    private ScaninaRegionSpecificationInterface $specification;

    public function __construct(
        ScaninaRegionRepositoryInterface $repository,
        ScaninaRegionSpecificationInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
    }

    public function execute($dto = null)
    {
        $response = $this->repository->get(
            $this->specification->getBusinessSector($dto)
        );

        $responseCountry = array_map(function ($city) {
            return new BrowseBusinessSectorResponseDto((array)$city);
        }, $response->data->rows);

        return (object)[
            'data' => $responseCountry,
            'paginate' => (object)[
                'total' => $response->data->metadata->total ?? 0,
                'count' => $response->data->metadata->count ?? 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
