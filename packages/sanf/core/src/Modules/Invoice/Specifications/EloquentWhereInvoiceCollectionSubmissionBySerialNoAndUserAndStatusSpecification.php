<?php

namespace Sanf\Core\Modules\Invoice\Specifications;

use Sanf\Core\Modules\Invoice\Models\InvoiceCollectionSubmissionModel;

final class EloquentWhereInvoiceCollectionSubmissionBySerialNoAndUserAndStatusSpecification
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

    public function buildQuery(InvoiceCollectionSubmissionModel $model)
    {
        $query = $model->newQuery()
            ->where('user_id', $this->userId)
            ->where('serial_no', $this->serialNo)
            ->where('status_id', $this->status);
        return $query;
    }
}
