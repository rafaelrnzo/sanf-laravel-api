<?php

namespace Sanf\Core\Modules\Promo;

class EloquentPromoRepository implements PromoRepositoryInterface
{

    /** @var PromoModel $model */
    protected $model;

    public function __construct(PromoModel $model)
    {
        $this->model = $model;
    }

    public function list($dto)
    {
        switch ($dto->sort_by) {
            case 'oldest':
                $orderBy = 'promo_sanf.created_at';
                $orderDir = 'ASC';
                break;
            case 'latest':
            default:
                $orderBy = 'promo_sanf.created_at';
                $orderDir = 'DESC';
        }

        $query = $this->model->newQuery();
        $total = $query->count();

        $lists = $query->select([
            'promo_sanf.xid',
            'promo_sanf.image_url',
            'promo_sanf.link_url',
            'promo_sanf.created_at',
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