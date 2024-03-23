<?php

namespace Sanf\Core\Modules\Insurance\Specifications;

use Sanf\Core\Modules\Insurance\Models\InsuranceClaimSubmissionModel;

final class EloquentWhereInsuranceClaimSubmissionBySerialNoAndUserAndStatusSpecification
{
    private int $userId;
    private string $serialNo;
    private array $status;

    public function __construct(string $serialNo, int $userId, array $status)
    {
        $this->serialNo = $serialNo;
        $this->userId = $userId;
        $this->status = $status;
    }

    public function buildQuery(InsuranceClaimSubmissionModel $model)
    {
        $query = $model->newQuery()
            ->where('user_id', $this->userId)
            ->where('serial_no', $this->serialNo)
            ->whereIn('status_id', $this->status);

        return $query;
    }
}
