<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Entities\PlafondEntityInterface;

interface PlafondRepositoryInterface
{
    public function getByProfile($xid): array;

    public function getHistoryByProfile($xid): array;

    public function getPlafondFactoringByProfile($xid): array;

    public function getByProfileAndType($profileXid, $typeId): ?PlafondEntityInterface;

    public function submitApplication($profileXid, $typeId, $code, $amount, $notes);
}
