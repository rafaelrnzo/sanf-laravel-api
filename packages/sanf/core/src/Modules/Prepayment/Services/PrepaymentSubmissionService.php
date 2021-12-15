<?php

namespace Sanf\Core\Modules\Prepayment\Services;

use Sanf\Core\Modules\Prepayment\Repositories\PrepaymentSubmissionRepositoryInterface;

class PrepaymentSubmissionService
{
    protected PrepaymentSubmissionRepositoryInterface $repository;

    public function __construct(PrepaymentSubmissionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}
