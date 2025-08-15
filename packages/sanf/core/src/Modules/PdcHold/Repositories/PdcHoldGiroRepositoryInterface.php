<?php

namespace Sanf\Core\Modules\PdcHold\Repositories;

/**
 * @since CR2025
 */
interface PdcHoldGiroRepositoryInterface
{
    public function add(array $fields);

    public function findToResume(array $xids, string $customer_id);

    public function updateByXid(string $xid, array $fields): bool;
}
