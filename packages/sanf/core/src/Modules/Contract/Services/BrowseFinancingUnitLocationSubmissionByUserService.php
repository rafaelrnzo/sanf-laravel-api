<?php

namespace Sanf\Core\Modules\Contract\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dtos\BrowseFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Dtos\BrowseFinancingUnitLocationSubmissionByUserResponseDto;
use Sanf\Core\Modules\Contract\Repositories\FinancingUnitLocationSubmissionRepositoryInterface;
use Sanf\Core\Modules\Contract\Specifications\FinancingUnitLocationSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\InternalApiClient;

final class BrowseFinancingUnitLocationSubmissionByUserService extends FinancingUnitLocationSubmissionByUserService implements ApplicationServiceInterface
{
    protected FinancingUnitLocationSubmissionSpecificationFactoryInterface $specificationFactory;
    protected InternalApiClient $internalApiClient;

    public function __construct(
        FinancingUnitLocationSubmissionRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        FinancingUnitLocationSubmissionSpecificationFactoryInterface $specificationFactory,
        InternalApiClient $internalApiClient
    ) {
        parent::__construct($repository, $userRepository);
        $this->specificationFactory = $specificationFactory;
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param BrowseFinancingUnitLocationSubmissionByUserRequestDto $dto
     * @return BrowseFinancingUnitLocationSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $submissions = $this->repository->query(
            $this->specificationFactory->paginateByUser($dto->userId, $dto->keyword, $dto->sortBy, $dto->skip, $dto->limit)
        );
        $total = $this->repository->size(
            $this->specificationFactory->paginateByUser($dto->userId, $dto->keyword)
        );

        //TODO
        $financingUnits = $this->internalApiClient->getFinancingUnits();

        $data = array_map(function ($item) {
            //foreach submissions
            return (object)[
                'contract_no' => $item->AGREE_NO,
                'xid' => $item->xid,
                'status' => $item->status, //$submission->status
                //TODO
                'createdAt' => $item->created_at,
                'updatedAt' => $item->updated_at,
            ];
        }, $financingUnits);

        return new BrowseFinancingUnitLocationSubmissionByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int)$total,
                'count' => count($data),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sortBy' => $dto->sortBy,
            ]
        ]);
    }
}
