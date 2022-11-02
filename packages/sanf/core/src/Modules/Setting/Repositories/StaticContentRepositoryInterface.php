<?php

namespace Sanf\Core\Modules\Setting\Repositories;

interface StaticContentRepositoryInterface
{
    public function findByXid($xid);
}
