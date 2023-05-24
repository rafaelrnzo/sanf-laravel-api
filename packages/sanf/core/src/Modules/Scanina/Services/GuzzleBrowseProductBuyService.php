<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyResponseDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class GuzzleBrowseProductBuyService implements ApplicationServiceInterface
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
        /** @var BrowseProductBuyRequestDto $dto */

        $response = $this->repository->get(
            $this->specification->getBuy($dto)
        );

        $responseProductBuy = array_map(function ($buyItem) {
            return new BrowseProductBuyResponseDto((array)$buyItem);
        }, $response->data->rows);

        return (object)[
            'data' => $responseProductBuy,
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
