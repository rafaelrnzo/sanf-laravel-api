<?php

namespace Sanf\Core\Modules\OnBoarding\Specifications;

use Sanf\Core\Modules\OnBoarding\Models\OnBoardingModel;

class EloquentBrowseOnBoardingSpecification
{
    private ?int $limit;
    private ?string $sortBy;

    public function __construct(int $limit = null, string $sortBy = null)
    {
        $this->limit = $limit;
        $this->sortBy = $sortBy;
    }

    public function buildQuery(OnBoardingModel $model)
    {
        switch ($this->sortBy) {
            case 'desc':
                $orderBy = 'order';
                $orderDirection = 'DESC';
                break;
            case 'asc':
            default:
                $orderBy = 'order';
                $orderDirection = 'ASC';
        }

        return $model->newQuery()
            ->orderBy($orderBy, $orderDirection)
            ->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            });
    }
}
