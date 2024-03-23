<?php

namespace Sanf\Core\Modules\Setting\Specifications;

use Sanf\Core\Modules\Setting\Models\FrequentlyAskQuestionCategoryModel;

class EloquentBrowseFrequentlyAskQuestionCategoryPageSpecification
{
    private ?string $keyword;
    private ?int $limit;
    private ?int $skip;
    private ?string $sortBy;

    public function __construct(?string $keyword, ?int $limit, ?int $skip, ?string $sortBy)
    {
        $this->keyword = $keyword;
        $this->limit = $limit;
        $this->skip = $skip;
        $this->sortBy = $sortBy;
    }

    public function buildQuery(FrequentlyAskQuestionCategoryModel $model)
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

        $keyword = $this->keyword;

        return $model->newQuery()
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('name', 'ilike', strtolower("%{$keyword}%"))
                    ->orWhereHas('faqs', function ($query) use ($keyword) {
                        $query->where('title', 'ilike', strtolower("%{$keyword}%"));
                    });
            })
            ->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })
            ->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })
            ->orderBy($orderBy, $orderDirection);
    }
}
