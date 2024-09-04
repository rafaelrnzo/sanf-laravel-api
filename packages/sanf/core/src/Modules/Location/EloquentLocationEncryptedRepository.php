<?php

namespace Sanf\Core\Modules\Location;

use Sanf\Core\Encryptions\SodiumEncryption;

class EloquentLocationEncryptedRepository implements LocationRepositoryInterface
{
    /** @var LocationEncryptedModel */
    protected $model;

    public function __construct(LocationEncryptedModel $model)
    {
        $this->model = $model;
    }

    public function list($dto)
    {
        $sodiumQuery = SodiumEncryption::query();

        switch ($dto->sort_by) {
            case 'name_asc':
                $orderBy = $sodiumQuery->selectRaw('name');
                $orderDir = 'ASC';
                break;
            case 'name_desc':
                $orderBy = $sodiumQuery->selectRaw('name');
                $orderDir = 'DESC';
                break;
            case 'earliest':
                $orderBy = 'id';
                $orderDir = 'ASC';
                break;
            case 'latest':
            default:
                $orderBy = 'id';
                $orderDir = 'DESC';
        }
        $query = $this->model->newQuery()
            ->where('level', '=', $dto->level)
            ->when($dto->keyword, function ($query) use ($dto, $sodiumQuery) {
                return $query->where($sodiumQuery->selectRaw('name'), 'ilike', "%{$dto->keyword}%");
            })
            ->when($dto->xid, function ($query) use ($dto) {
                return $query->where('xid', 'ilike', "{$dto->xid}%");
            });

        $total = $query->count();

        $lists = $query->select([
            'xid as location_code',
            'name',
            'level',
            'created_at',
            'version',
            'nonce',
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
