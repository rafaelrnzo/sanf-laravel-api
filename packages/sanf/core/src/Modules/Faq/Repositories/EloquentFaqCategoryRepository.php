<?php

namespace Sanf\Core\Modules\Faq\Repositories;

use Sanf\Core\Modules\Faq\Models\FaqCategoryModel;

class EloquentFaqCategoryRepository implements FaqCategoryRepositoryInterface
{
    protected $model;

    public function __construct(FaqCategoryModel $model)
    {
        $this->model = $model;
    }

    public function list($limit, $offset, $orderBy, $orderDirection, $search, $searchFaq)
    {
        return $this->model
            ->newQuery()
            ->select([
                'id',
                'name',
            ])
            ->when($search, function ($query) use ($search) {
                $query->whereRaw($search);
            })
            ->whereHas('faq', function ($query) use ($searchFaq) {
                $query->when($searchFaq, function ($query) use ($searchFaq) {
                    $query->whereRaw($searchFaq);
                });
            })
            ->limit($limit)
            ->offset($offset)
            ->orderBy($orderBy, $orderDirection)
            ->get();
    }

    public function findById($id)
    {
        return $this->model
            ->newQuery()
            ->select([
                'id',
                'name',
            ])
            ->where('id', $id)
            ->first();
    }
}
