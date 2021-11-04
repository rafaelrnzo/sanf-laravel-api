<?php


namespace Sanf\Core\Modules\Financing\Services;


use Carbon\CarbonImmutable;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Financing\Exceptions\FinancingApplicationLimitExceedException;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingFacilityRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingApplicationSpecificationFactoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class FinancingByUserService extends FinancingService
{
    protected AuthModel $userRepository;
    protected FinancingApplicationSpecificationFactoryInterface $financingSpecificationFactory;

    public function __construct(
        FinancingApplicationRepositoryInterface $financingApplicationRepository,
        FinancingMethodRepositoryInterface $financingMethodRepository,
        FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository,
        FinancingFacilityRepositoryInterface $financingFacilityRepository,
        FinancingApplicationSpecificationFactoryInterface $financingSpecificationFactory,
        AuthModel $userRepository
    ) {
        parent::__construct(
            $financingApplicationRepository,
            $financingMethodRepository,
            $financingPrerequisiteRepository,
            $financingFacilityRepository,
        );
        $this->userRepository = $userRepository;
        $this->financingSpecificationFactory = $financingSpecificationFactory;
    }

    protected function findUserOrFail($userId)
    {
        $user = $this->userRepository->newQuery()->find($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    protected function generateApplicationCode()
    {
        $now = CarbonImmutable::now();
        $year = $now->year;
        $month = $now->month;
        $count = $this->financingApplicationRepository->size($this->financingSpecificationFactory->findByMonth($now));
        $width = 6;
        if ($count >= 999999) {
            throw new FinancingApplicationLimitExceedException();
        }
        return "{$month}{$year}" . str_pad((string)$count++, $width, '0', STR_PAD_LEFT);
    }
}
