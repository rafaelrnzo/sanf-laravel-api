<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;

class GetMetadataFinancingByUserService extends FinancingByUserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        //TODO IMPLEMENTATION
        return (object) ['userId' => 1];
    }
}
