<?php

namespace Sanf\Core\Modules\Scanina\Repositories;

interface ProductCartRepositoryInterface
{
    public function query($specification);

    public function size($specification);

    public function create(array $request);

    public function findByXid(string $xid);
}
