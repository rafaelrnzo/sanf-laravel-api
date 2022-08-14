<?php

namespace Sanf\Core\Modules\Faq\Repositories;

use Sanf\Core\Modules\Faq\Models\FaqModel;

class EloquentFaqRepository implements FaqRepositoryInterface
{
    protected $model;

    public function __construct(FaqModel $model)
    {
        $this->model = $model;
    }

    public function list($limit, $offset, $orderBy, $orderDirection, $search = null)
    {
        return $this->model
            ->newQuery()
            ->select([
                'id',
                'title',
                'description',
                'category_id',
                'is_popular',
                'order'
            ])
            ->when($search, function ($query) use ($search) {
                $query->whereRaw($search);
            })
            ->limit($limit)
            ->offset($offset)
            ->orderBy($orderBy, $orderDirection)
            ->get();
    }
}
