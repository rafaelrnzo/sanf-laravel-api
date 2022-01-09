<?php

namespace Sanf\Core\Modules\User\Repositories;


interface UserRepositoryInterface
{
    public function findById($id);

    public function findByEmail($email);
}
