<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductServiceRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ProductServiceResponseDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class GuzzleBrowseProductServicesService implements ApplicationServiceInterface
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
        /** @var BrowseProductServiceRequestDto $dto */

        $response = $this->repository->get(
            $this->specification->getService($dto)
        );

        $responseProductService = array_map(function ($buyItem) {
            return new ProductServiceResponseDto((array)$buyItem);
        }, $response->data->rows);

        return (object)[
            'data' => $responseProductService,
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
