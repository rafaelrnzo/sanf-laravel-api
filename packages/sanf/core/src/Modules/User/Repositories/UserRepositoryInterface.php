<?php

namespace Sanf\Core\Modules\User\Repositories;

interface UserRepositoryInterface
{
    public function query($specification);

    public function find(array $filters);

    public function findById($id);

    public function findByEmail($email);

    public function existsByEmailAndStatusIds(string $email, array $statusIds): bool;

    public function findByEmailAndStatusIds(string $email, array $statusIds);

    public function existsByEmail(string $email): bool;

    public function create(array $data);

    public function update(array $data, $id): bool;
}
