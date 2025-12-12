<?php

namespace Sanf\Core\Modules\Disbursement\Repositories;

use Illuminate\Support\Collection;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementModel;

interface SparePartDisbursementRepositoryInterface
{
    /**
     * @param object $params
     * @return Collection<SparePartDisbursementModel>
     */
    public function list(object $params): Collection;

    public function listCount(object $params): int;
}
