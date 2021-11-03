<?php


namespace Sanf\Core\Modules\Financing\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\BrowseFinancingApplicationDto;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingFacilityRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingApplicationSpecificationFactoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class BrowseFinancingApplicationByUserService extends FinancingByUserService implements ApplicationServiceInterface
{
    protected FinancingApplicationSpecificationFactoryInterface $specificationFactory;
    public function __construct(
        FinancingApplicationRepositoryInterface $financingApplicationRepository,
        FinancingMethodRepositoryInterface $financingMethodRepository,
        FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository,
        FinancingFacilityRepositoryInterface $financingFacilityRepository,
        AuthModel $userRepository,
        FinancingApplicationSpecificationFactoryInterface $specificationFactory
    ) {
        $this->specificationFactory = $specificationFactory;
        parent::__construct(
            $financingApplicationRepository,
            $financingMethodRepository,
            $financingPrerequisiteRepository,
            $financingFacilityRepository,
            $userRepository
        );
    }

    /**
     * @param BrowseFinancingApplicationDto $dto
     * @return object
     * @throws \NbsPhp\Core\Exceptions\UserNotFoundException
     */
    public function execute($dto = null)
    {
        $this->findUserOrFail($dto->userId);
        $data = $this->financingApplicationRepository->query(
            $this->specificationFactory->paginateByUser($dto->userId, $dto->skip, $dto->limit, $dto->sortBy, $dto->keyword)
        );
        $total = $this->financingApplicationRepository->size(
            $this->specificationFactory->paginateByUser($dto->userId, $dto->skip, $dto->limit, $dto->sortBy, $dto->keyword)
        );

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => (int)$total,
                'count' => collect($data)->count(),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
