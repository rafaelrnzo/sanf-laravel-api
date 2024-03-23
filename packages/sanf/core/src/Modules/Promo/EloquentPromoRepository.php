<?php

namespace Sanf\Core\Modules\Promo;

use Carbon\Carbon;

class EloquentPromoRepository implements PromoRepositoryInterface
{
    /** @var PromoModel */
    protected $model;

    public function __construct(PromoModel $model)
    {
        $this->model = $model;
    }

    public function list($dto)
    {
        switch ($dto->sort_by) {
            case 'earliest':
            case 'oldest':
                $orderBy = 'promo_sanf.created_at';
                $orderDir = 'ASC';
                break;
            case 'latest':
            case 'newest':
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
            ->when($dto->timestamp, function ($query) use ($dto) {
                return $query->where('promo_sanf.created_at', '>', Carbon::createFromTimestamp($dto->timestamp));
            })
            ->where('xid', '!=', PromoModel::SANF_SCANINA)
            ->get();

        return [
            'total' => $total,
            'count' => $lists->count(),
            'lists' => $lists,
        ];
    }

    public function findByXid(string $xid)
    {
        return $this->model
            ->newQuery()
            ->where('xid', '=', $xid)
            ->first();
    }
}
