<?php

namespace Sanf\Core\Modules\Financing\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Financing\Models\FinancingApplicationEncryptedModel;
use Sanf\Core\Modules\Financing\Models\FinancingObjectModel;

class EloquentFinancingApplicationEncryptedRepository extends AbstractEloquentRepository implements FinancingApplicationRepositoryInterface
{
    protected FinancingApplicationEncryptedModel $model;
    protected array $encryptedJsonFields;

    public function __construct(FinancingApplicationEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedJsonFields = [
            'profile_snapshot',
        ];
    }

    public function findById($id)
    {
        $model = $this->model->newQuery()->with(['status', 'objects', 'facility', 'method'])->find($id);

        return $this->stripEloquentModel($model);
    }

    public function findByXid($userId, $xid, $applicationXid)
    {
        $model = $this->model->newQuery()
            ->where(function ($query) use ($userId, $xid) {
                return $query->where('user_id', $userId)
                    ->orWhere('profile_xid', $xid);
            })
            ->where('xid', $applicationXid)
            ->with(['status', 'objects', 'facility', 'method'])->first();

        return $this->stripEloquentModel($model);
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($models);
    }

    public function add($fields)
    {
        $sodiumQuery = SodiumEncryption::query();

        $sodiumQuery->multipleBeginTransaction();

        try {
            $fieldFinancingObjects = $fields['financing_objects'];
            $fieldFinancingHistory = $fields['financing_history'];
            $fieldFinancingApplication = collect($fields)->except(['financing_objects', 'financing_history'])->toArray();
            $fieldFinancingApplication = $this->encryptBeforeCreate($fieldFinancingApplication);

            $model = $this->model->newQuery()->forceCreate($fieldFinancingApplication);

            $financingObjects = array_map(function ($item) {
                return new FinancingObjectModel($item);
            }, $fieldFinancingObjects);
            $model->objects()->saveMany($financingObjects);

            $fieldFinancingHistory['application_id'] = $model->id;
            $fieldFinancingHistory = SodiumEncryption::encryptor()->encryptMultipleData($fieldFinancingHistory, ['created_by']);

            $model->history()->create($fieldFinancingHistory);

            $model->refresh();

            $result = $this->stripEloquentModel($model);

            $sodiumQuery->multipleCommit();

            return $result;
        } catch (\Throwable $th) {
            $sodiumQuery->multipleRollBack();

            throw $th;
        }
    }

    private function encryptBeforeCreate(array $data): array
    {
        return SodiumEncryption::encryptor()->encryptMultipleData($data, [], $this->encryptedJsonFields);
    }

    public function update($fields, $specification = null)
    {
        if (!is_null($specification)) {
            $model = $specification->buildQuery($this->model)->update($fields);

            return $this->stripEloquentModel($model);
        }

        $fields = $this->encryptBeforeUpdate($fields, $fields['id']);

        $model = $this->model->newQuery()->where('id', $fields['id'])->update($fields);

        return $this->stripEloquentModel($model);
    }

    private function encryptBeforeUpdate(array $data, $id): array
    {
        $model = $this->model->newQuery()->find($id);

        return $model->encryptor()->encryptMultipleData($data, [], $this->encryptedJsonFields);
    }

    public function remove($specification)
    {
        return $specification->buildQuery($this->model)->delete();
    }

    public function removeById($id)
    {
        return $this->model->newQuery()->where('id', $id)->delete();
    }

    public function removeByXid($xid)
    {
        return $this->model->newQuery()->where('xid', $xid)->delete();
    }

    public function size($specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->model)->count();
        }

        return $this->model->newQuery()->select('id')->count();
    }
}
