<?php

namespace Sanf\Core\Modules\Invoice\Services;

use Sanf\Core\Modules\Invoice\Repositories\InvoiceCollectionSubmissionRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class InvoiceCollectionSubmissionByUserService
{
    protected InvoiceCollectionSubmissionRepositoryInterface $repository;
    protected UserRepositoryInterface $userRepository;

    public function __construct(InvoiceCollectionSubmissionRepositoryInterface $repository, UserRepositoryInterface $userRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }
}
