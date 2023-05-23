<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductFilterModelResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductFilterRequestDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class GuzzleBrowseProductFilterModelService implements ApplicationServiceInterface
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
        /** @var BrowseProductFilterRequestDto $dto */

        $response = $this->repository->get(
            $this->specification->getFilterModel($dto)
        );

        $responseProductFilterModel = array_map(function ($category) {
            return new BrowseProductFilterModelResponseDto((array)$category);
        }, $response->data->rows);

        return (object)[
            'data' => $responseProductFilterModel,
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
