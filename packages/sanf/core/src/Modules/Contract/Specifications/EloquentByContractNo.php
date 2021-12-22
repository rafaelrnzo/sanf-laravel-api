<?php

namespace Sanf\Core\Modules\Contract\Specifications;

use Carbon\Carbon;
use Sanf\Core\Modules\Contract\Models\FinancingUnitLocationSubmissionModel;

final class EloquentByContractNo
{
    private int $userId;
    private string $contractNo;

    public function __construct(
        int $userId,
        string $contractNo
    ) {
        $this->userId = $userId;
        $this->contractNo = $contractNo;
    }

    public function buildQuery(FinancingUnitLocationSubmissionModel $model)
    {
        return $model->newQuery()
            ->where('user_id', $this->userId)
            ->where('contract_no', $this->contractNo)
            ->with('status');
    }
}
