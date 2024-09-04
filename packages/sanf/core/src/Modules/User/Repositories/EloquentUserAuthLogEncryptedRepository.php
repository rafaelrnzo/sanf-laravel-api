<?php

namespace Sanf\Core\Modules\User\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\UserAuthLogEncryptedModel;

class EloquentUserAuthLogEncryptedRepository extends AbstractEloquentRepository implements UserAuthLogRepositoryInterface
{
    private UserAuthLogEncryptedModel $model;
    private array $encryptedFields;

    public function __construct(UserAuthLogEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedFields = [
            'email',
            'created_by',
        ];
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($models);
    }

    public function size($specification)
    {
        return $specification->buildQuery($this->model)->count();
    }

    public function findByXid($xid)
    {
        $models = $this->model->newQuery()
            ->where('xid', '=', $xid)
            ->first();

        return $this->stripEloquentModel($models);
    }

    public function findByUserId($userId)
    {
        $models = $this->model->newQuery()
            ->where('user_id', '=', $userId)
            ->first();

        return $this->stripEloquentModel($models);
    }

    public function findByUserIdAndStatus($userId, $statusId)
    {
        $models = $this->model->newQuery()
            ->where('user_id', '=', $userId)
            ->where('status_id', '=', $statusId)
            ->first();

        return $this->stripEloquentModel($models);
    }

    public function create($request)
    {
        $model = $this->model->forceCreate($this->encryptBeforeCreate($request));

        return $this->stripEloquentModel($model->fresh());
    }

    private function encryptBeforeCreate(array $data): array
    {
        $encryptor = SodiumEncryption::encryptor();

        foreach ($this->encryptedFields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            $data[$field] = $encryptor->encrypt($data[$field]);
        }

        $data['nonce'] = $encryptor->nonce()->getNonceHex();

        return $data;
    }

    public function update($fields, $specification = null)
    {
        $fields = $this->encryptBeforeUpdate($fields);

        if (!is_null($specification)) {
            $model = $specification->buildQuery($this->model)->update($fields);

            return $this->stripEloquentModel($model);
        }

        $model = $this->model->newQuery()->where('id', $fields['id'])->update($fields);

        return $this->stripEloquentModel($model);
    }

    private function encryptBeforeUpdate(array $data): array
    {
        $model = $this->model->find($data['id']);

        foreach ($this->encryptedFields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            $data[$field] = $model->encryptor()->encrypt($data[$field]);
        }

        return $data;
    }
}
