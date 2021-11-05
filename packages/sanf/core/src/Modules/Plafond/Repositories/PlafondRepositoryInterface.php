<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Entities\PlafondEntity;

interface PlafondRepositoryInterface
{
    public function getByProfile($xid): array;

    public function getByProfileAndType($profileXid, $typeId): ?PlafondEntity;

    public function submitApplication($profileXid, $typeId, $amount);
}
