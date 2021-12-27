<?php

namespace Sanf\Core\Modules\Contract\Specifications;

use Sanf\Core\Modules\Contract\Enums\FinancingUnitLocationSubmissionStatusEnum;
use Sanf\Core\Modules\Contract\Models\FinancingUnitLocationSubmissionModel;

final class EloquentWhereContractNumberAndIsProcess
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
            ->where('status_id', FinancingUnitLocationSubmissionStatusEnum::IN_PROGRESS)
            ->with('status');
    }
}
