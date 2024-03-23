<?php

namespace Sanf\Core\Modules\Invoice\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Invoice\Dtos\BrowseInvoiceCollectionSubmissionByUserRequestDto;
use Sanf\Core\Modules\Invoice\Dtos\BrowseInvoiceCollectionSubmissionByUserResponseDto;
use Sanf\Core\Modules\Invoice\Repositories\InvoiceCollectionSubmissionRepositoryInterface;
use Sanf\Core\Modules\Invoice\Specifications\InvoiceCollectionSubmissionSpecificationFactoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

final class BrowseInvoiceCollectionSubmissionByUserService extends InvoiceCollectionSubmissionByUserService implements ApplicationServiceInterface
{
    protected InvoiceCollectionSubmissionSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        InvoiceCollectionSubmissionRepositoryInterface $invoiceCollectionSubmissionRepository,
        UserRepositoryInterface $userRepository,
        InvoiceCollectionSubmissionSpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct($invoiceCollectionSubmissionRepository, $userRepository);
        $this->specificationFactory = $specificationFactory;
    }

    /**
     * @param BrowseInvoiceCollectionSubmissionByUserRequestDto $dto
     * @return BrowseInvoiceCollectionSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $result = $this->repository->query(
            $this->specificationFactory->paginateByUserAndProfile($dto->userId, $dto->profileXid, $dto->keyword, $dto->statusId, $dto->sortBy, $dto->skip, $dto->limit)
        );
        $total = $this->repository->size(
            $this->specificationFactory->paginateByUserAndProfile($dto->userId, $dto->profileXid, $dto->keyword, $dto->statusId)
        );

        $data = array_map(function ($item) {
            return (object) [
                'id' => $item->id,
                'xid' => $item->xid,
                'status' => $item->status,
                'contractNo' => $item->contract_no,
                'serialNo' => $item->serial_no,
                'pickupDate' => CarbonImmutable::make($item->pickup_date),
                'brandTypeModel' => $item->brand_type_model,
                'year' => $item->year,
                'createdAt' => $item->created_at,
                'updatedAt' => $item->updated_at,
            ];
        }, $result);

        return new BrowseInvoiceCollectionSubmissionByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int) $total,
                'count' => count($data),
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sortBy' => $dto->sortBy,
            ],
        ]);
    }
}
