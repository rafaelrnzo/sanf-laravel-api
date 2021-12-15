<?php

namespace Sanf\Core\Modules\Prepayment\Services;

use Sanf\Core\Modules\Prepayment\Repositories\PrepaymentSubmissionRepositoryInterface;

class PrepaymentSubmissionByByUserService extends PrepaymentSubmissionService
{
    public function __construct(PrepaymentSubmissionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
