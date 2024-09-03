<?php

namespace Sanf\Core\Modules\User\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\AuthEncryptedModel;

class EloquentUserEncryptedRepository extends AbstractEloquentRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(AuthEncryptedModel $model)
    {
        $this->model = $model;
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
        return $this->model->newQuery()->where('username', $email)->first();
    }

    public function existsByEmailAndStatusIds(string $email, array $statusIds): bool
    {
        $sodiumQuery = SodiumEncryption::query();

        return $this->model->newQuery()
            ->where($sodiumQuery->selectRaw('username'), $email)
            ->whereIn('status_id', $statusIds)
            ->exists();
    }

    public function forceCreate(array $data) {
        $user = $this->model->newQuery()->forceCreate(
            $this->reformatBeforeCreate($data)
        );

        return $user->fresh();
    }

    private function reformatBeforeCreate(array $data): array
    {
        $encryptedFields = [
            'username',
            'full_name',
            'landline_number',
            'phone_number',
            'company_name',
        ];

        $encryptor = SodiumEncryption::encryptor();

        foreach ($encryptedFields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            $data[$field] = $encryptor->encrypt($data[$field]);
        }

        $data['nonce'] = $encryptor->nonce()->getNonceHex();

        return $data;
    }
}
