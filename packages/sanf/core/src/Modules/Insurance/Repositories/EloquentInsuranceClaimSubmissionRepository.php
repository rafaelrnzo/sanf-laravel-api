<?php

namespace Sanf\Core\Modules\Insurance\Repositories;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Insurance\Models\InsuranceClaimSubmissionHistoryModel;
use Sanf\Core\Modules\Insurance\Models\InsuranceClaimSubmissionModel;

class EloquentInsuranceClaimSubmissionRepository extends AbstractEloquentRepository implements InsuranceClaimSubmissionRepositoryInterface
{
    protected InsuranceClaimSubmissionModel $model;
    protected InsuranceClaimSubmissionHistoryModel $historyModel;

    public function __construct(InsuranceClaimSubmissionModel $model, InsuranceClaimSubmissionHistoryModel $historyModel)
    {
        $this->model = $model;
        $this->historyModel = $historyModel;
    }

    public function findById($id)
    {
        $model = $this->model->newQuery()->with(['status'])->find($id);

        return $this->stripEloquentModel($model);
    }

    public function findByXid($xid)
    {
        $model = $this->model->newQuery()->where('xid', $xid)->with(['status'])->first();

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
