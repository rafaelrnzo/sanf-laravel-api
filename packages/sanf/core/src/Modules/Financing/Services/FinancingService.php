<?php


namespace Sanf\Core\Modules\Financing\Services;

use Carbon\Carbon;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;

class FinancingService
{
    protected FinancingApplicationRepositoryInterface $financingApplicationRepository;
    protected FinancingMethodRepositoryInterface $financingMethodRepository;
    protected FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository;

    public function __construct(
        FinancingApplicationRepositoryInterface $financingApplicationRepository,
        FinancingMethodRepositoryInterface $financingMethodRepository,
        FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository
    ) {
        $this->financingApplicationRepository = $financingApplicationRepository;
        $this->financingMethodRepository = $financingMethodRepository;
        $this->financingPrerequisiteRepository = $financingPrerequisiteRepository;
    }

    protected function generateApplicationCode()
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;
        return "{$month}{$year}";
    }
}
