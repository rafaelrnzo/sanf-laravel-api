<?php

namespace Sanf\Core\Modules\Contract\Repositories;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Contract\Models\FinancingUnitLocationSubmissionHistoryModel;
use Sanf\Core\Modules\Contract\Models\FinancingUnitLocationSubmissionModel;

class EloquentFinancingUnitLocationSubmissionRepository extends AbstractEloquentRepository implements FinancingUnitLocationSubmissionRepositoryInterface
{
    protected FinancingUnitLocationSubmissionModel $model;
    protected FinancingUnitLocationSubmissionHistoryModel $historyModel;

    public function __construct(FinancingUnitLocationSubmissionModel $model, FinancingUnitLocationSubmissionHistoryModel $historyModel)
    {
        $this->model = $model;
        $this->historyModel = $historyModel;
    }

    public function findById($id)
    {
        $model = $this->model->newQuery()->with(['user', 'status'])->find($id);

        return $this->stripEloquentModel($model);
    }

    public function findByXid($xid)
    {
        $model = $this->model->newQuery()->where('xid', $xid)->with(['user', 'status'])->first();

        return $this->stripEloquentModel($model);
    }

    public function findByContractNo($userId, $contractNo)
    {
        $model = $this->model->newQuery()->where([
            'user_id' => $userId,
            'contract_no' =>$contractNo,
        ])->with(['user', 'status'])->first();

        return $this->stripEloquentModel($model);
    }

    public function findByContractNoAndSerialNo($userId, $contractNo, $serialNo)
    {
        $model = $this->model->newQuery()->where([
            'user_id' => $userId,
            'contract_no' =>$contractNo,
            'serial_no' => $serialNo,
        ])->with(['user', 'status'])->first();

        return $this->stripEloquentModel($model);
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($models);
    }

    public function add($fields)
    {
        $model = DB::transaction(function () use ($fields) {
            $model = $this->model->newQuery()->forceCreate($fields);
            $this->historyModel->newQuery()->forceCreate([
                'submission_id' => $model->id,
                'status_id' => $model->status_id,
                'created_by' => new \stdClass(), //TODO SNAPSHOT
            ]);

            return $model;
        });

        return $this->stripEloquentModel($model);
    }

    public function update($fields)
    {
        $model = $this->model->newQuery()->where('id', $fields['id'])->update($fields);

        return $this->stripEloquentModel($model);
    }

    public function remove($fields)
    {
        return $this->model->newQuery()->where('id', $fields['id'])->delete();
    }

    public function size($specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->model)->count();
        }

        return $this->model->newQuery()->select('id')->count();
    }
}
