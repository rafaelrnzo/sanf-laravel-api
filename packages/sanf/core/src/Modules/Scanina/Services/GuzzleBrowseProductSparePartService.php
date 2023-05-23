<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSparePartRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSparepartResponseDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class GuzzleBrowseProductSparePartService implements ApplicationServiceInterface
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
        /** @var BrowseProductSparePartRequestDto $dto */

        $response = $this->repository->get(
            $this->specification->getSparePart($dto)
        );

        $responseProductSparePart = array_map(function ($buyItem) {
            return new BrowseProductSparepartResponseDto((array)$buyItem);
        }, $response->data->rows);

        return (object)[
            'data' => $responseProductSparePart,
            'paginate' => (object)[
                'total' => 0,
                'count' => 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
