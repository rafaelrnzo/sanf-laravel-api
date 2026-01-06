<?php

namespace Sanf\Core\Modules\Disbursement\UseCases;

use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Core\Modules\Disbursement\Exceptions\SparePartDisbrusementInvalidStatusChangesException;
use Sanf\Core\Modules\Disbursement\Exceptions\SparePartDisbrusementStatusLockedException;
use Sanf\Core\Modules\Disbursement\Payloads\StatusFromCoreSparePartDisbursementPayload;
use Sanf\Core\Modules\Disbursement\Repositories\SparePartDisbursementRepositoryInterface;
use Sanf\Core\Modules\Disbursement\Supports\SparePartDisbursementStatusResolver;
use Sanf\Integration\Modules\SanfCore\Enums\SanfCoreSparePartDisbursementStatusEnum;

final class StatusSparePartDisbursementUseCase
{
    private SparePartDisbursementRepositoryInterface $repository;

    public function __construct(SparePartDisbursementRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function updateFromCore(StatusFromCoreSparePartDisbursementPayload $payload)
    {
        $disbursement = $this->repository->find([
            'batch_number' => $payload->batch_id,
            'customer_id' => $payload->cust_id,
            'customer_id_sanfind' => $payload->cust_id_sanfind,
        ]);

        if (!$disbursement) {
            throw new ResourceNotFoundException('Spare Part Disbursement not found');
        }

        if ($disbursement->status_id != SparePartDisbursementStatusEnum::WAITING_VALIDATION) {
            throw new SparePartDisbrusementStatusLockedException();
        }

        $allowedTargetStatuses = [
            SanfCoreSparePartDisbursementStatusEnum::VALID,
            SanfCoreSparePartDisbursementStatusEnum::NOT_VALID,
        ];

        if (!in_array($payload->status_batch_id, $allowedTargetStatuses)) {
            throw new SparePartDisbrusementInvalidStatusChangesException();
        }

        $statusId = SparePartDisbursementStatusResolver::mapFromCore($payload->status_batch_id);

        $this->repository->update(
            ['id' => $disbursement->id],
            ['status_id' => $statusId]
        );
    }
}
