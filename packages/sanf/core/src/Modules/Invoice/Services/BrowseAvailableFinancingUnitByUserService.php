<?php

namespace Sanf\Core\Modules\Invoice\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Invoice\Dtos\BrowseFinancingUnitByUserRequestDto;
use Sanf\Core\Modules\Invoice\Dtos\BrowseFinancingUnitByUserResponseDto;
use Sanf\Core\Modules\Invoice\Enums\InvoiceCollectionSubmissionStatusEnum;
use Sanf\Core\Modules\Invoice\Repositories\InvoiceCollectionSubmissionRepositoryInterface;
use Sanf\Core\Modules\Invoice\Specifications\InvoiceCollectionSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

final class BrowseAvailableFinancingUnitByUserService implements ApplicationServiceInterface
{
    protected InvoiceCollectionSubmissionRepositoryInterface $invoiceCollectionSubmissionRepository;
    protected UserRepositoryInterface $userRepository;
    protected InvoiceCollectionSubmissionSpecificationFactoryInterface $specificationFactory;
    protected InternalApiClient $apiClient;

    /**
     * BrowseAvailableFinancingUnitByUserService constructor.
     * @param InvoiceCollectionSubmissionRepositoryInterface $invoiceCollectionSubmissionRepository
     * @param UserRepositoryInterface $userRepository
     * @param InvoiceCollectionSubmissionSpecificationFactoryInterface $specificationFactory
     * @param InternalApiClient $apiClient
     */
    public function __construct(
        InvoiceCollectionSubmissionRepositoryInterface $invoiceCollectionSubmissionRepository,
        UserRepositoryInterface $userRepository,
        InvoiceCollectionSubmissionSpecificationFactoryInterface $specificationFactory,
        InternalApiClient $apiClient
    ) {
        $this->invoiceCollectionSubmissionRepository = $invoiceCollectionSubmissionRepository;
        $this->userRepository = $userRepository;
        $this->specificationFactory = $specificationFactory;
        $this->apiClient = $apiClient;
    }

    /**
     * @param BrowseFinancingUnitByUserRequestDto $dto
     * @return BrowseFinancingUnitByUserResponseDto
     */
    public function execute($dto = null)
    {
        try {
            $result = $this->apiClient->getFinancingUnitOfInvoiceCollection(
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
                'serialNo' => $item->SERIAL_NO,
                'brandTypeModel' => $item->BTM,
                'year' => $item->YEAR,
            ];
        }, $result->data);

        $data = array_filter($data, function ($datum) use ($dto) {
            return !$this->invoiceCollectionSubmissionRepository->query(
                $this->specificationFactory->whereBySerialNoAndUserAndStatus(
                    $datum->serialNo,
                    $dto->userId,
                    InvoiceCollectionSubmissionStatusEnum::NOT_ELIGIBLE_FOR_SUBMISSION
                )
            );
        });

        return new BrowseFinancingUnitByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int)($result->total ?? $result->count),
                'count' => count($data),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sortBy' => $dto->sortBy,
            ]
        ]);
    }
}
