<?php

namespace Sanf\Core\Modules\Astra;

class EloquentProductAstraRepository implements ProductAstraRepositoryInterface
{

    /** @var ProductAstraModel $model */
    protected $model;

    public function __construct(ProductAstraModel $model)
    {
        $this->model = $model;
    }

    public function list($dto)
    {
        switch ($dto->sort_by) {
            case 'oldest':
                $orderBy = 'promo_astra.created_at';
                $orderDir = 'ASC';
                break;
            case 'latest':
            default:
                $orderBy = 'promo_astra.created_at';
                $orderDir = 'DESC';
        }

        $query = $this->model->newQuery();
        $total = $query->count();

        $lists = $query->select([
            'promo_astra.xid',
            'promo_astra.image_url',
            'promo_astra.link_url',
            'promo_astra.created_at',
        ])
            ->orderBy($orderBy, $orderDir)
            ->skip($dto->skip)
            ->limit($dto->limit)
            ->get();

        return [
            'total' => $total,
            'count' => $lists->count(),
            'lists' => $lists,
        ];
    }
}