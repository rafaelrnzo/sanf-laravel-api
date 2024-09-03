<?php

namespace Sanf\Core\Modules\User\Repositories;

interface UserRepositoryInterface
{
    public function query($specification);

    public function findById($id);

    public function findByEmail($email);

    public function existsByEmailAndStatusIds(string $email, array $statusIds): bool;

    public function forceCreate(array $data);
}
