<?php


namespace Sanf\Core\Modules\Financing\Services;

use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;

class FinancingService
{
    protected FinancingMethodRepositoryInterface $financingMethodRepository;
    protected FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository;

    public function __construct(FinancingMethodRepositoryInterface $financingMethodRepository,FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository)
    {
        $this->financingMethodRepository = $financingMethodRepository;
        $this->financingPrerequisiteRepository = $financingPrerequisiteRepository;
    }
}
