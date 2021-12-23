<?php

namespace Sanf\Core\Modules\Invoice\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Invoice\Dtos\BrowseFinancingUnitByUserRequestDto;
use Sanf\Core\Modules\Invoice\Dtos\BrowseFinancingUnitByUserResponseDto;
use Sanf\Core\Modules\Invoice\Repositories\InvoiceCollectionSubmissionRepositoryInterface;
use Sanf\Core\Modules\Invoice\Specifications\InvoiceCollectionSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
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
    public function __construct(InvoiceCollectionSubmissionRepositoryInterface $invoiceCollectionSubmissionRepository, UserRepositoryInterface $userRepository, InvoiceCollectionSubmissionSpecificationFactoryInterface $specificationFactory, InternalApiClient $apiClient)
    {
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
        $result = $this->apiClient->getFinancingUnitOfInvoiceCollection(
            $dto->profileXid,
            $dto->skip,
            $dto->limit,
            $dto->sortBy,
            $dto->timestamp,
            $dto->keyword
        );
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
                $this->specificationFactory->whereStillProcessedBySerialNoAndUser($datum->serialNo, $dto->userId)
            );
        });

        return new BrowseFinancingUnitByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int)$result->count,
                'count' => count($data),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sortBy' => $dto->sortBy,
            ]
        ]);
    }
}
