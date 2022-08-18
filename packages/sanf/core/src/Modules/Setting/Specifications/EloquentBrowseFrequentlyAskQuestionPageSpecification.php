<?php

namespace Sanf\Core\Modules\Setting\Specifications;

use Sanf\Core\Modules\Setting\Models\FrequentlyAskQuestionModel;

class EloquentBrowseFrequentlyAskQuestionPageSpecification
{
    private ?int $categoryId;
    private ?bool $isPopular;
    private ?string $keyword;
    private ?int $limit;
    private ?int $skip;
    private ?string $sortBy;

    public function __construct(
        ?int $categoryId,
        ?bool $isPopular,
        ?string $keyword,
        ?int $limit,
        ?int $skip,
        ?string $sortBy
    ) {
        $this->categoryId = $categoryId;
        $this->isPopular = $isPopular;
        $this->keyword = $keyword;
        $this->limit = $limit;
        $this->skip = $skip;
        $this->sortBy = $sortBy;
    }

    public function buildQuery(FrequentlyAskQuestionModel $model)
    {
        switch ($this->sortBy) {
            case 'titleAsc':
                $orderBy = 'title';
                $orderDirection = 'ASC';
                break;
            case 'titleDesc':
                $orderBy = 'title';
                $orderDirection = 'DESC';
                break;
            case 'orderAsc':
                $orderBy = 'order';
                $orderDirection = 'ASC';
                break;
            case 'orderDesc':
            default:
                $orderBy = 'order';
                $orderDirection = 'DESC';
        }

        $isPopular = $this->isPopular;
        $categoryId = $this->categoryId;
        $keyword = $this->keyword;
        return $model->newQuery()
            ->with('category')
            ->whereHas('category')
            ->when($isPopular, function ($query) use ($isPopular) {
                $query->where('is_popular', '=', $isPopular);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', '=', $categoryId);
            })
            ->when($keyword, function ($query) use ($keyword, $isPopular, $categoryId) {
                $query->where('title', 'like', strtolower("%{$keyword}%"))
                    ->when($isPopular, function ($query) use ($isPopular) {
                        $query->where('is_popular', '=', $isPopular);
                    })
                    ->when($categoryId, function ($query) use ($categoryId) {
                        $query->where('category_id', '=', $categoryId);
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
