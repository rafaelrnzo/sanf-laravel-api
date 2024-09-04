<?php

namespace Sanf\Core\Modules\User\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\AuthEncryptedModel;

class EloquentUserEncryptedRepository extends AbstractEloquentRepository implements UserRepositoryInterface
{
    protected $model;
    protected array $encryptedFields;

    public function __construct(AuthEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedFields = [
            'username',
            'full_name',
            'landline_number',
            'phone_number',
            'company_name',
        ];
    }

    public function query($specification)
    {
        $records = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($records);
    }

    public function findById($id)
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByEmail($email)
    {
        $sodiumQuery = SodiumEncryption::query();

        return $this->model->newQuery()->where($sodiumQuery->selectRaw('username'), $email)->first();
    }

    public function existsByEmailAndStatusIds(string $email, array $statusIds): bool
    {
        $sodiumQuery = SodiumEncryption::query();

        return $this->model->newQuery()
            ->where($sodiumQuery->selectRaw('username'), $email)
            ->whereIn('status_id', $statusIds)
            ->exists();
    }

    public function existsByEmail(string $email): bool
    {
        $sodiumQuery = SodiumEncryption::query();

        return $this->model->newQuery()
            ->where($sodiumQuery->selectRaw('username'), $email)
            ->exists();
    }

    public function create(array $data) {
        $user = $this->model->newQuery()->forceCreate(
            $this->encryptBeforeCreate($data)
        );

        return $user->fresh();
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

    public function update(array $data, $id): bool
    {
        return $this->model->newQuery()
            ->whereId($id)
            ->update($this->encryptBeforeUpdate($data, $id));
    }

    private function encryptBeforeUpdate(array $data, $id): array
    {
        $user = $this->model->newQuery()->find($id);

        foreach ($this->encryptedFields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            $data[$field] = $user->encryptor()->encrypt($data[$field]);
        }

        return $data;
    }
}
