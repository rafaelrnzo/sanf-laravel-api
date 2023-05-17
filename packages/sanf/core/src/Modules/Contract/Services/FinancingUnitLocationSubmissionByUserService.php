<?php

namespace Sanf\Core\Modules\Contract\Services;

use Sanf\Core\Modules\Contract\Repositories\FinancingUnitLocationSubmissionRepositoryInterface;
use Sanf\Core\Modules\Contract\Specifications\FinancingUnitLocationSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class FinancingUnitLocationSubmissionByUserService
{
    protected FinancingUnitLocationSubmissionRepositoryInterface $repository;
    protected AuthModel $userRepository;
    protected SanfCoreApiClient $internalApiClient;

    protected FinancingUnitLocationSubmissionSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        FinancingUnitLocationSubmissionRepositoryInterface $repository,
        SanfCoreApiClient $internalApiClient,
        AuthModel $userRepository,
        FinancingUnitLocationSubmissionSpecificationFactoryInterface $specificationFactory
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->internalApiClient = $internalApiClient;
        $this->specificationFactory = $specificationFactory;
    }
}
