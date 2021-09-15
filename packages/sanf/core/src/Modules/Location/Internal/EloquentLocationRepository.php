<?php


namespace Sanf\Core\Modules\Location\Internal;


class EloquentLocationRepository implements LocationRepositoryInterface
{

    /** @var LocationModel $query */
    protected $model;

    public function __construct(LocationModel $model)
    {
        $this->model = $model;
    }

    public function list($dto)
    {
        switch ($dto->sort_by) {
            case 'name_asc':
                $orderBy = 'm_location.name';
                $orderDir = 'ASC';
                break;
            case 'name_desc':
                $orderBy = 'm_location.name';
                $orderDir = 'DESC';
                break;
            case 'oldest':
                $orderBy = 'm_location.id';
                $orderDir = 'ASC';
                break;
            case 'latest':
            default:
                $orderBy = 'm_location.id';
                $orderDir = 'DESC';
        }
        $query = $this->model->newQuery()
            ->where('m_location.administrative_area_id', '=', $dto->adm_area_id)
            ->when($dto->keyword, function ($query) use ($dto) {
                return $query->where('m_location.name', 'ilike', "%{$dto->keyword}%");
            })
            ->when($dto->parent_xid, function ($query) use ($dto) {
                return $query->where('m_location.location_code', 'like', "{$dto->parent_xid}-%");
            })
            ->when($dto->xid, function ($query) use ($dto) {
                return $query->where('m_location.location_code', '=', $dto->xid);
            });

        $total = $query->count();

        $lists = $query->select([
            'm_location.location_code',
            'm_location.name',
            'm_location.level',
            'm_location.created_at',
            'm_location.version',
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