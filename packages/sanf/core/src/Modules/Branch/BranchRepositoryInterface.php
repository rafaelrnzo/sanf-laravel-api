<?php

namespace Sanf\Core\Modules\Branch;

interface BranchRepositoryInterface
{
    public function list($limit, $offset);
}
