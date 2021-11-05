<?php

namespace Sanf\Core\Modules\Plafond\Services;

use Sanf\Core\Modules\Plafond\Repositories\PlafondRepositoryInterface;

class PlafondService
{
    protected PlafondRepositoryInterface $repository;

    public function __construct(PlafondRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}
