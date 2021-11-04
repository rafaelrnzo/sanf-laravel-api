<?php


namespace Sanf\Core\Modules\Financing\Services;

use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingFacilityRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;

class FinancingService
{
    protected FinancingApplicationRepositoryInterface $financingApplicationRepository;
    protected FinancingMethodRepositoryInterface $financingMethodRepository;
    protected FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository;
    protected FinancingFacilityRepositoryInterface $financingFacilityRepository;

    public function __construct(
        FinancingApplicationRepositoryInterface $financingApplicationRepository,
        FinancingMethodRepositoryInterface $financingMethodRepository,
        FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository,
        FinancingFacilityRepositoryInterface $financingFacilityRepository
    ) {
        $this->financingApplicationRepository = $financingApplicationRepository;
        $this->financingMethodRepository = $financingMethodRepository;
        $this->financingPrerequisiteRepository = $financingPrerequisiteRepository;
        $this->financingFacilityRepository = $financingFacilityRepository;
    }
}
