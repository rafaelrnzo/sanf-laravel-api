<?php

namespace Sanf\Core\Modules\User\Repositories;

interface RegistrationOTPRepositoryInterface
{
    public function findLatestActive(int $userId, string $purpose);
    public function findLatestNotUsed(int $userId, string $purpose);

    public function create(array $data);

    public function update(int $id, array $data): bool;

    public function deleteOthers(int $userId, string $purpose, int $excludeId);
}
