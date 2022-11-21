<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingCategoryRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingCategorySpecificationFactoryInterface;

final class BrowseFinancingCategoryService implements ApplicationServiceInterface
{
    private FinancingCategoryRepositoryInterface $financingCategoryRepository;
    private FinancingCategorySpecificationFactoryInterface $specificationFactory;

    public function __construct(
        FinancingCategoryRepositoryInterface $financingCategoryRepository,
        FinancingCategorySpecificationFactoryInterface $specificationFactory
    ) {
        $this->financingCategoryRepository = $financingCategoryRepository;
        $this->specificationFactory = $specificationFactory;
    }

    public function execute($dto = null)
    {
        $data = $this->financingCategoryRepository->query(
            $this->specificationFactory->paginate($dto->keyword, $dto->skip, $dto->limit, $dto->sortBy)
        );

        $total = $this->financingCategoryRepository->size(
            $this->specificationFactory->paginate($dto->keyword, null, null, null)
        );

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => $total,
                'count' => collect($data)->count(),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
