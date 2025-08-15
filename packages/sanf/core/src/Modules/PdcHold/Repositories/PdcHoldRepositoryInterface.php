<?php

namespace Sanf\Core\Modules\PdcHold\Repositories;

/**
 * @since CR2025
 */
interface PdcHoldRepositoryInterface
{
    public function query($specification);

    public function findByXid(string $xid);

    public function add(array $fields);

    public function size($specification = null);
}
