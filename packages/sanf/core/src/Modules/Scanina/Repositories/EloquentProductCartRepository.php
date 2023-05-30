<?php

namespace Sanf\Core\Modules\Scanina\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Scanina\Models\ScaninaProductCartModel;

class EloquentProductCartRepository extends AbstractEloquentRepository implements ProductCartRepositoryInterface
{

    private ScaninaProductCartModel $model;

    public function __construct(ScaninaProductCartModel $model)
    {
        $this->model = $model;
    }

    public function query($specification): array
    {
        $records = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($records);
    }

    public function size($specification): int
    {
        return $specification->buildQuery($this->model)->count();
    }

    public function create(array $request)
    {
        return $this->model->newQuery()->forceCreate($request);
    }

    public function findByXid(string $xid)
    {
        return $this->model->newQuery()->where('xid', '=', $xid)->first();
    }
}
