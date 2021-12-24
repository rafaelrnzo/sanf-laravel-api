<?php

namespace Sanf\Core\Modules\Contract\Services;

use Sanf\Core\Modules\Contract\Repositories\FinancingUnitLocationSubmissionRepositoryInterface;
use Sanf\Core\Modules\Contract\Specifications\FinancingUnitLocationSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\InternalApiClient;

class FinancingUnitLocationSubmissionByUserService
{
    protected FinancingUnitLocationSubmissionRepositoryInterface $repository;
    protected AuthModel $userRepository;
    protected InternalApiClient $internalApiClient;

    protected FinancingUnitLocationSubmissionSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        FinancingUnitLocationSubmissionRepositoryInterface $repository,
        InternalApiClient $internalApiClient,
        AuthModel $userRepository,
        FinancingUnitLocationSubmissionSpecificationFactoryInterface $specificationFactory
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->internalApiClient = $internalApiClient;
        $this->specificationFactory = $specificationFactory;
    }
}
