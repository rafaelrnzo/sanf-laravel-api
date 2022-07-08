<?php

namespace Sanf\Core\Modules\Contract\Repositories;

interface ESignRepositoryInterface
{
    public function findUserById(int $id);

    public function findUserByEmail(string $email);

    public function createUser(array $data);

    public function updateUser(int $id, array $data);
}
