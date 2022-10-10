<?php


namespace Sanf\Core\Modules\Product;


class EloquentProductRepository implements ProductRepositoryInterface
{

    /** @var ProductModel $model */
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
                'image'
            ])
            ->when($search, function ($query) use ($search) {
                return $query->whereRaw($search);
            })
            ->limit($limit)
            ->offset($offset)
            ->orderBy('id', 'asc')
            ->get();
    }
}