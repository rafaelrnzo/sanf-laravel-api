<?php

namespace Sanf\Core\Modules\Prepayment\Repositories;

interface PrepaymentSubmissionRepositoryInterface
{
    public function add($fields);

    public function whereContractNo($contractNo): array;
}
