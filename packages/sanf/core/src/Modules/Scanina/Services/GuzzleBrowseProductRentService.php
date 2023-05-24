<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class GuzzleBrowseProductRentService implements ApplicationServiceInterface
{

    private ScaninaProductRepositoryInterface $repository;
    private ScaninaProductSpecificationInterface $specification;

    public function __construct(
        ScaninaProductRepositoryInterface $repository,
        ScaninaProductSpecificationInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
    }

    public function execute($dto = null)
    {
        /** @var BrowseProductRentRequestDto $dto */

        $response = $this->repository->get(
            $this->specification->getRent($dto)
        );

        $responseProductRent = array_map(function ($buyItem) {
            $buyItem->startDateAvailable = (int)optional($buyItem)->startDateAvailable;
            $buyItem->endDateAvailable = (int)optional($buyItem)->endDateAvailable;

            return new BrowseProductRentResponseDto((array)$buyItem);
        }, $response->data->rows);

        return (object)[
            'data' => $responseProductRent,
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
