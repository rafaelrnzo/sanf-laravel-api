<?php

namespace Sanf\Core\Modules\User\Repositories;

interface UserAuthLogRepositoryInterface
{
    public function query($specification);

    public function size($specification);

    public function create($request);
}
