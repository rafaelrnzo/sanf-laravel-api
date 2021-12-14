<?php

namespace Sanf\Core\Modules\Insurance\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;

final class BrowseInsuranceClaimSubmissionByUserService implements ApplicationServiceInterface
{
    /*For Case Browse
    protected InsuranceClaimSubmissionSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        InsuranceClaimSubmissionRepositoryInterface $repository,
        InsuranceClaimSubmissionSpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct($repository);
        $this->specificationFactory = $specificationFactory;
    }
    /*

    /**
     * @param BrowseInsuranceClaimSubmissionByUserRequestDto $dto
     * @return BrowseInsuranceClaimSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        /* For Case Browse
        $result = $this->repository->query(
            $this->specificationFactory->paginate($dto->keyword, $dto->sortBy, $dto->skip, $dto->limit)
        );
        $total = $this->repository->size(
            $this->specificationFactory->paginate($dto->keyword)
        );

        $data = array_map(function ($item) {
            return (object)[
                'xid' => $item->xid,
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
        */

        /* For Case Read/Add/Update
        $entity = $this->repository->findByXid($dto->xid);
        if (is_null($entity)) {
            throw new InsuranceClaimSubmissionNotFoundException();
        }
        return new BrowseInsuranceClaimSubmissionByUserResponseDto([
            'id' => $entity->getId(),
        ]);
        */
    }
}
