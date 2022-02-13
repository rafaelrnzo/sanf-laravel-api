<?php

namespace Sanf\Core\Modules\Insurance\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Insurance\Dtos\BrowseInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Dtos\BrowseInsuranceClaimSubmissionByUserResponseDto;
use Sanf\Core\Modules\Insurance\Repositories\InsuranceClaimSubmissionRepositoryInterface;
use Sanf\Core\Modules\Insurance\Specifications\InsuranceClaimSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

final class BrowseInsuranceClaimSubmissionByUserService extends InsuranceClaimSubmissionByUserService implements ApplicationServiceInterface
{
    protected InsuranceClaimSubmissionSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        InsuranceClaimSubmissionRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        ProfileRepositoryInterface $profileRepository,
        InsuranceClaimSubmissionSpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct($repository, $userRepository, $profileRepository);
        $this->specificationFactory = $specificationFactory;
    }

    /**
     * @param BrowseInsuranceClaimSubmissionByUserRequestDto $dto
     * @return BrowseInsuranceClaimSubmissionByUserResponseDto
     */
    public
    function execute($dto = null)
    {
        $result = $this->repository->query(
            $this->specificationFactory->paginateByUserAndProfile($dto->userId, $dto->profileXid, $dto->keyword, $dto->statusId, $dto->sortBy, $dto->skip, $dto->limit)
        );
        $total = $this->repository->size(
            $this->specificationFactory->paginateByUserAndProfile($dto->userId, $dto->profileXid, $dto->keyword, $dto->statusId)
        );

        $data = array_map(function ($item) {
            return (object)[
                'id' => $item->id,
                'xid' => $item->xid,
                'status' => $item->status,
                'serialNo' => $item->serial_no,
                'polisNo' => $item->polis_no,
                'brandTypeModel' => $item->brand_type_model,
                'year' => $item->year,
                'createdAt' => $item->created_at,
                'updatedAt' => $item->updated_at,
            ];
        }, $result);

        return new BrowseInsuranceClaimSubmissionByUserResponseDto([
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
