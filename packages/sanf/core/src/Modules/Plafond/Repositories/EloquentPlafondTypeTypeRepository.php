<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Models\PlafondTypeModel;

final class EloquentPlafondTypeTypeRepository implements PlafondTypeRepositoryInterface
{
    private PlafondTypeModel $plafondTypeModel;

    public function __construct(PlafondTypeModel $plafondTypeModel)
    {
        $this->plafondTypeModel = $plafondTypeModel;
    }

    public function types($dto)
    {
        $query = $this->plafondTypeModel
            ->newQuery()
            ->select(['id', 'title']);

        $total = $query->count();

        switch ($dto->sort_by) {
            case 'title_asc':
                $order = $query->orderBy('title');
                break;
            case 'title_desc':
                $order = $query->orderByDesc('title');
                break;
            default:
                $order = $query->orderBy('id');
                break;
        }
        $lists = $order->limit($dto->limit)
            ->skip($dto->skip)
            ->get();

        return [
            'total' => $total,
            'count' => $lists->count(),
            'lists' => $lists,
        ];
    }
}
