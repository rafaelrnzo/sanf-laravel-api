<?php

namespace Sanf\Core\Modules\Setting\Specifications;

use Sanf\Core\Modules\Setting\Models\FaqCategoryModel;

class EloquentBrowseFaqCategorySpecification
{
    private ?int $limit;
    private ?int $skip;
    private ?string $sortBy;

    public function __construct(int $limit = null, int $skip = null, string $sortBy = null)
    {
        $this->limit = $limit;
        $this->skip = $skip;
        $this->sortBy = $sortBy;
    }

    public function buildQuery(FaqCategoryModel $model)
    {
        switch ($this->sortBy) {
            case 'desc':
                $orderBy = 'name';
                $orderDirection = 'DESC';
                break;
            case 'asc':
            default:
                $orderBy = 'name';
                $orderDirection = 'ASC';
        }

        return $model->newQuery()
            ->orderBy($orderBy, $orderDirection)
            ->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })
            ->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            });
    }
}
