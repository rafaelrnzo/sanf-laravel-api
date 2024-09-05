<?php

namespace Sanf\Core\Modules\HttpLog\Repositories;

interface AuditHttpLogRepositoryInterface
{
    public function create(array $data);
}
