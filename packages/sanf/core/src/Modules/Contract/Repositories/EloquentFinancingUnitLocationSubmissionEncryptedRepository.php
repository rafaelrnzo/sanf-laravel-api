<?php

namespace Sanf\Core\Modules\Contract\Repositories;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Contract\Models\FinancingUnitLocationSubmissionEncryptedModel;
use Sanf\Core\Modules\Contract\Models\FinancingUnitLocationSubmissionHistoryEncryptedModel;

class EloquentFinancingUnitLocationSubmissionEncryptedRepository extends AbstractEloquentRepository implements FinancingUnitLocationSubmissionRepositoryInterface
{
    protected FinancingUnitLocationSubmissionEncryptedModel $model;
    protected FinancingUnitLocationSubmissionHistoryEncryptedModel $historyModel;
    protected array $encryptedFieldsSubmission;
    protected array $encryptedJsonFieldsSubmissionHistory;

    public function __construct(FinancingUnitLocationSubmissionEncryptedModel $model, FinancingUnitLocationSubmissionHistoryEncryptedModel $historyModel)
    {
        $this->model = $model;
        $this->historyModel = $historyModel;

        $this->encryptedFieldsSubmission = [
            'submitted_location_metadata',
            'city_name',
        ];

        $this->encryptedJsonFieldsSubmissionHistory = [
            'created_by',
        ];
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
            $fields = SodiumEncryption::encryptor()->encryptMultipleData($fields, $this->encryptedFieldsSubmission);

            $model = $this->model->newQuery()->forceCreate($fields);

            $historyFields = SodiumEncryption::encryptor()->encryptMultipleData(
                [
                    'submission_id' => $model->id,
                    'status_id' => $model->status_id,
                    'created_by' => new \stdClass(), //TODO SNAPSHOT
                ],
                [],
                $this->encryptedJsonFieldsSubmissionHistory
            );

            $this->historyModel->newQuery()->forceCreate($historyFields);

            return $model->fresh();
        });

        return $this->stripEloquentModel($model);
    }

    public function update($fields)
    {
        $model = $this->model->newQuery()->whereId($fields['id'])->first();

        if (is_null($model)) {
            return null;
        }

        $fields = $model->encryptor()->encryptMultipleData($fields, $this->encryptedFieldsSubmission);

        $this->model->newQuery()->whereId($fields['id'])->update($fields);

        return $this->stripEloquentModel($model->fresh());
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
