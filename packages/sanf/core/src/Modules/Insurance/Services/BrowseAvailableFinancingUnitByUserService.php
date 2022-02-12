<?php

namespace Sanf\Core\Modules\Insurance\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Insurance\Dtos\BrowseFinancingUnitByUserResponseDto;
use Sanf\Core\Modules\Insurance\Enums\InsuranceClaimSubmissionStatusEnum;
use Sanf\Core\Modules\Insurance\Repositories\InsuranceClaimSubmissionRepositoryInterface;
use Sanf\Core\Modules\Insurance\Specifications\InsuranceClaimSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\Invoice\Dtos\BrowseFinancingUnitByUserRequestDto;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

final class BrowseAvailableFinancingUnitByUserService implements ApplicationServiceInterface
{
    protected InternalApiClient $apiClient;
    protected InsuranceClaimSubmissionRepositoryInterface $insuranceClaimSubmissionRepository;
    protected InsuranceClaimSubmissionSpecificationFactoryInterface $specificationFactory;

    /**
     * BrowseContractByUserService constructor.
     * @param InternalApiClient $apiClient
     */
    public function __construct(
        InternalApiClient $apiClient,
        InsuranceClaimSubmissionRepositoryInterface $insuranceClaimSubmissionRepository,
        InsuranceClaimSubmissionSpecificationFactoryInterface $specificationFactory
    ) {
        $this->apiClient = $apiClient;
        $this->insuranceClaimSubmissionRepository = $insuranceClaimSubmissionRepository;
        $this->specificationFactory = $specificationFactory;
    }

    /**
     * @param BrowseFinancingUnitByUserRequestDto $dto
     * @return BrowseFinancingUnitByUserResponseDto
     */
    public function execute($dto = null)
    {
        try {
            $result = $this->apiClient->getFinancingUnitOfInsurance(
                $dto->profileXid,
                $dto->skip,
                $dto->limit,
                $dto->sortBy,
                $dto->timestamp,
                $dto->keyword
            );
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return new BrowseFinancingUnitByUserResponseDto([
                'data' => [],
                'paginate' => [
                    'total' => 0,
                    'count' => 0,
                    'skip' => (int)$dto->skip,
                    'limit' => (int)$dto->limit,
                    'sortBy' => $dto->sortBy,
                ]
            ]);
        }


        $data = array_map(function ($item) {
            return (object)[
                'contractNo' => $item->AGREE_NO,
                'polisNo' => $item->POLIS_NO,
                'serialNo' => $item->SERIAL_NO,
                'brandTypeModel' => $item->BTM,
                'year' => $item->YEAR ?? '',
            ];
        }, $result->data);

        $data = array_filter($data, function ($datum) use ($dto) {
            return !$this->insuranceClaimSubmissionRepository->query(
                $this->specificationFactory->whereBySerialNoAndUserAndStatus(
                    $datum->serialNo,
                    $dto->userId,
                    InsuranceClaimSubmissionStatusEnum::NOT_ELIGIBLE_FOR_SUBMISSION)
            );
        });

        return new BrowseFinancingUnitByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int)($result->total ?? $result->count),
                'count' => (int)$result->count,
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sortBy' => $dto->sortBy,
            ]
        ]);
    }
}
