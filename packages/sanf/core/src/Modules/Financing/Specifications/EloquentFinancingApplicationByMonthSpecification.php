<?php

namespace Sanf\Core\Modules\Financing\Specifications;

use Sanf\Core\Modules\Financing\Models\FinancingApplicationModel;

class EloquentFinancingApplicationByMonthSpecification
{
    private \DateTimeImmutable $dateTime;

    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function buildQuery(FinancingApplicationModel $model)
    {
        $query = $model->newQuery()
            ->whereMonth('created_at', $this->dateTime);

        return $query;
    }
}
