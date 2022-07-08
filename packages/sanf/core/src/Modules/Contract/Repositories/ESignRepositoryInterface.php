<?php

namespace Sanf\Core\Modules\Contract\Repositories;


interface ESignRepositoryInterface
{
    public function findUserByEmail(string $email);
}
