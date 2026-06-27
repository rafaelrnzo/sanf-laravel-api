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

    public function find(array $filters)
    {
        return $this->model->newQuery()->where($filters)->first();
    }

    public function findById($id)
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByEmail($email)
    {
        $usernameIndex = SodiumEncryption::hash($email);

        return $this->model->newQuery()->where('username_index', $usernameIndex)->first();
    }

    public function existsByEmailAndStatusIds(string $email, array $statusIds): bool
    {
        $usernameIndex = SodiumEncryption::hash($email);

        return $this->model->newQuery()
            ->where('username_index', $usernameIndex)
            ->whereIn('status_id', $statusIds)
            ->exists();
    }

    public function findByEmailAndStatusIds(string $email, array $statusIds)
    {
        $usernameIndex = SodiumEncryption::hash($email);

        return $this->model->newQuery()
            ->where('username_index', $usernameIndex)
            ->whereIn('status_id', $statusIds)
            ->first();
    }

    public function existsByEmail(string $email): bool
    {
        $usernameIndex = SodiumEncryption::hash($email);

        return $this->model->newQuery()
            ->where('username_index', $usernameIndex)
            ->exists();
    }

    public function create(array $data)
    {
        $user = $this->model->newQuery()->forceCreate(
            $this->encryptBeforeCreate($data)
        );

        return $user->fresh();
    }

    private function encryptBeforeCreate(array $data): array
    {
        $encryptor = SodiumEncryption::encryptor();

        $plainUsername = $data['username'] ?? null;


        foreach ($this->encryptedFields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            $data[$field] = $encryptor->encrypt($data[$field]);
        }

        if (!is_null($plainUsername)) {
            $data['username_index'] = SodiumEncryption::hash($plainUsername);
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

        $plainUsername = $data['username'] ?? null;

        foreach ($this->encryptedFields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            $data[$field] = $user->encryptor()->encrypt($data[$field]);
        }

        if (!is_null($plainUsername)) {
            $data['username_index'] = SodiumEncryption::hash($plainUsername);
        }

        return $data;
    }
}
