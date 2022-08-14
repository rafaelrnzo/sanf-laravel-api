<?php

namespace Sanf\Core\Modules\Setting\Services;

use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Repositories\OnBoardingRepositoryInterface;
use Sanf\Core\Modules\Setting\Specifications\OnBoardingSpecificationFactoryInterface;

class BrowseOnBoardingService implements ApplicationServiceInterface
{
    protected OnBoardingRepositoryInterface $repository;
    protected OnBoardingSpecificationFactoryInterface $specification;

    public function __construct(
        OnBoardingRepositoryInterface $repository,
        OnBoardingSpecificationFactoryInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
    }

    public function execute($dto = null)
    {
        $query = $this->repository->query(
            $this->specification->browse($dto->limit, $dto->sortBy)
        );

        return array_map(function ($item) {
            return (object) [
                'id' => $item->id,
                'title' => $item->title ?? null,
                'description' => $item->description ?? null,
                'imageFile' => $item->image_file ?? null,
                'createdAt' => $item->created_at ?? Carbon::now(),
            ];
        }, $query);
    }
}
