<?php

namespace Sanf\Core\Modules\User\Repositories;

use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\UserOAuthEncryptedModel;

class EloquentUserOAuthEncryptedRepository implements UserOAuthRepositoryInterface
{
    protected $model;
    protected array $encryptedFields;

    public function __construct(UserOAuthEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedFields = [
            'name',
        ];
    }

    public function create(array $data) {
        $model = $this->model->newQuery()->forceCreate(
            SodiumEncryption::encryptor()->encryptMultipleData($data, $this->encryptedFields)
        );

        return $model->fresh();
    }

    public function findByProvider(string $provider, string $providerId)
    {
        return $this->model->newQuery()
            ->where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();
    }

    public function update(array $data, $id)
    {
        $model = $this->model->newQuery()->find($id);

        if (is_null($model)) {
            return null;
        }

        $model->encryptor()->encryptMultipleData($data, $this->encryptedFields);

        return $model->fresh();
    }
}
