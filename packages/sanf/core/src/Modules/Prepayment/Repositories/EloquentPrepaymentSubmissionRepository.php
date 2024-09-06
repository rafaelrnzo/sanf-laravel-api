<?php

namespace Sanf\Core\Modules\Prepayment\Repositories;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Prepayment\Models\PrepaymentSubmissionHistoryEncryptedModel;
use Sanf\Core\Modules\Prepayment\Models\PrepaymentSubmissionModel;

class EloquentPrepaymentSubmissionRepository extends AbstractEloquentRepository implements PrepaymentSubmissionRepositoryInterface
{
    protected $model;
    protected $historyModel;

    public function __construct(PrepaymentSubmissionModel $model, PrepaymentSubmissionHistoryEncryptedModel $historyModel)
    {
        $this->model = $model;
        $this->historyModel = $historyModel;
    }

    public function add($fields)
    {
        $model = DB::transaction(function () use ($fields) {
            $model = $this->model->newQuery()->forceCreate($fields);
            $this->historyModel
                ->newQuery()
                ->forceCreate(
                    SodiumEncryption::encryptor()->encryptBulkData(
                        [
                            'submission_id' => $model->id,
                            'status_id' => $model->status_id,
                            'created_by' => new \stdClass(), //TODO SNAPSHOT
                        ],
                        [],
                        ['created_by'],
                    )
                );

            return $model;
        });

        return $this->stripEloquentModel($model);
    }

    public function whereContractNo($contractNo): array
    {
        $models = $this->model->newQuery()->where('contract_no', $contractNo)->get();

        return $this->stripEloquentModel($models);
    }
}
