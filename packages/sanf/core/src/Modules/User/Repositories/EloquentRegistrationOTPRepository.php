<?php

namespace Sanf\Core\Modules\User\Repositories;

use Sanf\Core\Modules\User\Models\RegistrationOTPEncryptedModel;
use Carbon\Carbon;
use Sanf\Core\Encryptions\SodiumEncryption;


class EloquentRegistrationOTPRepository implements RegistrationOTPRepositoryInterface
{
    protected $model;
    protected array $encryptedFields;

    public function __construct(RegistrationOTPEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedFields = [
            'email',
        ];
    }

    public function findLatestActive(int $userId, string $purpose)
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->where('expired_at', '>', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function findLatestNotUsed(int $userId, string $purpose)
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function create(array $data)
    {
        $data = SodiumEncryption::encryptor()->encryptMultipleData(
            $data,
            $this->encryptedFields
        );

        if (isset($data['email'])) {
            $data['email_index'] = SodiumEncryption::hash($data['email']);
        }

        return $this->model->newQuery()->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $model = $this->model->newQuery()->find($id);

        if (!$model) {
            return false;
        }

        $data = $model->encryptor()->encryptMultipleData(
            $data,
            $this->encryptedFields
        );

        if (isset($data['email'])) {
            $data['email_index'] = SodiumEncryption::hash($data['email']);
        }

        return $model->update($data);
    }

    public function deleteOthers(int $userId, string $purpose, int $excludeId)
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->where('purpose', $purpose)
            ->where('id', '!=', $excludeId)
            ->delete();
    }
}
