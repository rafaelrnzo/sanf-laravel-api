<?php

namespace Sanf\Core\Modules\Financing\Specifications;

use Sanf\Core\Modules\Financing\Models\FinancingCategoryModel;

class EloquentPaginateFinancingCategorySpecification
{
    private ?string $keyword;
    private ?int $skip;
    private ?int $limit;
    private ?string $sortBy;

    public function __construct(?string $keyword, ?int $skip, ?int $limit, ?string $sortBy)
    {
        $this->keyword = $keyword;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->sortBy = $sortBy;
    }

    public function buildQuery(FinancingCategoryModel $model)
    {
        switch ($this->sortBy) {
            case 'oldest':
                $orderBy = 'financing_category.created_at';
                $orderDirection = 'ASC';
                break;
            case 'latest':
            default:
                $orderBy = 'financing_category.created_at';
                $orderDirection = 'DESC';
        }

        return $model->newQuery()
            ->orderBy($orderBy, $orderDirection)
            ->when($this->keyword, function ($query) {
                return $query->where('title', 'ilike', "%{$this->keyword}%");
            })->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            });
    }
}
