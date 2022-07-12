<?php

namespace Sanf\Core\Modules\Contract\Repositories;

interface ESignRepositoryInterface
{
    public function findUserById(int $id);

    public function findUserByUserId(int $id);

    public function findUserByEmail(string $email);

    public function documentQuery($specification);

    public function documentSize($specification = null);

    public function createUser(array $data);

    public function updateUser(int $id, array $data);
}
