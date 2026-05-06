<?php

namespace Sanf\Core\Modules\User\Repositories;

use Sanf\Core\Modules\User\Models\RegistrationOTPEncryptedModel;
use Carbon\Carbon;

class EloquentRegistrationOTPRepository implements RegistrationOTPRepositoryInterface
{
    protected $model;

    public function __construct(RegistrationOTPEncryptedModel $model)
    {
        $this->model = $model;
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
        return $this->model->newQuery()->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->newQuery()->where('id', $id)->update($data);
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
