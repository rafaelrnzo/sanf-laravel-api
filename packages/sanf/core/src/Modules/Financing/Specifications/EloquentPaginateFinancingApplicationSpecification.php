<?php

namespace Sanf\Core\Modules\Financing\Specifications;

use Sanf\Core\Modules\Financing\Models\FinancingApplicationModel;

class EloquentPaginateFinancingApplicationSpecification
{
    private ?int $skip;
    private ?int $limit;
    private ?string $sortBy;
    private ?string $keyword;

    public function __construct(?int $skip, ?int $limit, ?string $sortBy, ?string $keyword)
    {
        $this->skip = $skip;
        $this->limit = $limit;
        $this->sortBy = $sortBy;
        $this->keyword = $keyword;
    }

    public function buildQuery(FinancingApplicationModel $model)
    {
        switch ($this->sortBy) {
            case 'earliest':
            case 'oldest':
                $orderBy = 'created_at';
                $orderDirection = 'ASC';
                break;
            case 'latest':
            case 'newest':
            default:
                $orderBy = 'created_at';
                $orderDirection = 'DESC';
        }

        $query = $model->newQuery()
            ->orderBy($orderBy, $orderDirection)
            ->when($this->keyword, function ($query) {
                return $query->where('name', "ILIKE", '%' . $this->keyword . '%');
            })->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            });

        return $query;
    }
}
