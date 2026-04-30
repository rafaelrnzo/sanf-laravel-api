<?php

namespace Sanf\Core\Modules\Product;

class EloquentProductRepository implements ProductRepositoryInterface
{
    /** @var ProductModel */
    protected $model;

    public function __construct(ProductModel $model)
    {
        $this->model = $model;
    }

    public function list($limit, $offset, $search = null)
    {
        return $this->model
            ->newQuery()
            ->select([
                'id',
                'title',
                'description',
                'financing_method_id',
                'image',
            ])
            ->when(!empty($search), function ($query) use ($search) {
                if (isset($search['id'])) {
                    $query->where('id', $search['id']);
                }
                if (isset($search['title'])) {
                    $query->where('title', 'ilike', '%' . $search['title'] . '%');
                }
                return $query;
            })
            ->limit($limit)
            ->offset($offset)
            ->orderBy('id', 'asc')
            ->get();
    }
}
