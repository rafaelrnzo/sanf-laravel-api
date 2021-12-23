<?php

namespace Sanf\Core\Modules\Invoice\Specifications;

use Sanf\Core\Modules\Invoice\Enums\InvoiceCollectionSubmissionStatusEnum;
use Sanf\Core\Modules\Invoice\Models\InvoiceCollectionSubmissionModel;

final class EloquentWhereInvoiceCollectionSubmissionStillProcessedByContractAndUserSpecification
{
    private int $userId;
    private string $serialNo;

    public function __construct(string $serialNo, int $userId)
    {
        $this->serialNo = $serialNo;
        $this->userId = $userId;
    }

    public function buildQuery(InvoiceCollectionSubmissionModel $model)
    {
        $query = $model->newQuery()
            ->where('user_id', $this->userId)
            ->where('serial_no', $this->serialNo)
            ->where('status_id', InvoiceCollectionSubmissionStatusEnum::PROCESSED);
        return $query;
    }
}
