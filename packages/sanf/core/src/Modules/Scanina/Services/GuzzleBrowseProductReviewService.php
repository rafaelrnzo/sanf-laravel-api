<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductReviewResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductFilterRequestDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class GuzzleBrowseProductReviewService implements ApplicationServiceInterface
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
            $this->specification->getCustomerReview($dto)
        );

        $reviews = array_map(function ($review) {
            return new BrowseProductReviewResponseDto((array)$review);
        }, $response->data->rows);

        return (object)[
            'data' => $reviews,
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
