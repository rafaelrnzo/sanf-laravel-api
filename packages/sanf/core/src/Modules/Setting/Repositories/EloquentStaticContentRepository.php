<?php

namespace Sanf\Core\Modules\Setting\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Setting\Models\StaticContentModel;

class EloquentStaticContentRepository extends AbstractEloquentRepository implements StaticContentRepositoryInterface
{
    private StaticContentModel $model;

    public function __construct(StaticContentModel $model)
    {
        $this->model = $model;
    }

    public function findByXid($xid)
    {
        return $this->model->newQuery()->where('xid', $xid)->first();
    }
}
