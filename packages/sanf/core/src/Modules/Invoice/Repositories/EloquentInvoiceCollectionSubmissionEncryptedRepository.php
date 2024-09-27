<?php

namespace Sanf\Core\Modules\Invoice\Repositories;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Invoice\Models\InvoiceCollectionSubmissionHistoryEncryptedModel;
use Sanf\Core\Modules\Invoice\Models\InvoiceCollectionSubmissionModel;

class EloquentInvoiceCollectionSubmissionEncryptedRepository extends AbstractEloquentRepository implements InvoiceCollectionSubmissionRepositoryInterface
{
    protected InvoiceCollectionSubmissionModel $model;
    protected InvoiceCollectionSubmissionHistoryEncryptedModel $historyModel;
    protected array $encryptedJsonFieldsSubmissionHistory;

    public function __construct(InvoiceCollectionSubmissionModel $model, InvoiceCollectionSubmissionHistoryEncryptedModel $historyModel)
    {
        $this->model = $model;
        $this->historyModel = $historyModel;
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

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($models);
    }

    public function add($fields)
    {
        $model = DB::transaction(function () use ($fields) {
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

        return $this->stripEloquentModel($model->fresh());
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
