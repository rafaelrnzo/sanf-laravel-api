<?php

namespace Sanf\Core\Modules\PdcHold\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\PdcHold\Models\PdcHoldModel;

/**
 * @since CR2025
 */
class PdcHoldEloquentRepository extends AbstractEloquentRepository implements PdcHoldRepositoryInterface
{
    protected PdcHoldModel $pdcHoldModel;

    public function __construct(PdcHoldModel $pdcHoldModel)
    {
        $this->pdcHoldModel = $pdcHoldModel;
    }

    public function query($builder)
    {
        $pdcHoldCollection = $builder->build($this->pdcHoldModel)->get();

        return $this->stripEloquentModel($pdcHoldCollection);
    }

    public function findByXid(string $xid)
    {
        $model = $this->pdcHoldModel->newQuery()
            ->where('xid', $xid)
            ->with('giros')
            ->with('resume_giros')
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function add(array $fields)
    {
        $model = $this->pdcHoldModel->newQuery()->create($fields);

        return $this->stripEloquentModel($model);
    }

    public function size($specification = null)
    {
        if (!is_null($specification)) {
            return $specification->build($this->pdcHoldModel)->count();
        }

        return $this->pdcHoldModel->newQuery()->select('id')->count();
    }
}
