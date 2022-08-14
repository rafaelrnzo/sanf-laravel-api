<?php

namespace Sanf\Core\Modules\Setting\Specifications;

use Sanf\Core\Modules\Setting\Models\FrequentlyAskQuestionModel;

class EloquentBrowseFrequentlyAskQuestionSpecification
{
    private ?string $keyword;
    private ?int $limit;
    private ?int $skip;
    private ?string $sortBy;

    public function __construct(string $keyword = null, int $limit = null, int $skip = null, string $sortBy = null)
    {
        $this->keyword = $keyword;
        $this->limit = $limit;
        $this->skip = $skip;
        $this->sortBy = $sortBy;
    }

    public function buildQuery(FrequentlyAskQuestionModel $model)
    {
        switch ($this->sortBy) {
            case 'asc':
                $orderBy = 'created_at';
                $orderDirection = 'ASC';
                break;
            case 'desc':
            default:
                $orderBy = 'created_at';
                $orderDirection = 'DESC';
        }

        $keyword = $this->keyword;
        return $model->newQuery()
            ->with('category')
            ->whereHas('category')
            ->when($keyword, function ($query) use($keyword) {
                $query->where('name', 'like', strtolower("%{$keyword}%"));
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
