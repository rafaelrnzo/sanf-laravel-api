<?php

namespace Sanf\Core\Modules\PdcHold\Repositories;

use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;

/**
 * @since CR2025
 */
interface PdcHoldRepositoryInterface
{
    public function query($specification);

    public function findByXid(string $xid);

    public function add(array $fields);

    public function size($specification = null);

    public function updateStatus(string $xid, PdcHoldStatusEnum $status): bool;

    public function destroyNotHavingGiros(array $filters): int;
}
