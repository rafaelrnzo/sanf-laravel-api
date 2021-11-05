<?php

namespace Sanf\Core\Modules\Plafond\Services;

use Sanf\Core\Modules\Plafond\Repositories\PlafondRepositoryInterface;

class PlafondByUserService extends PlafondService
{
    public function __construct(PlafondRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
