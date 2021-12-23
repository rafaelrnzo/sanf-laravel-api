<?php

namespace Sanf\Core\Modules\Invoice\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Invoice\Dtos\AddInvoiceCollectionSubmissionByUserRequestDto;
use Sanf\Core\Modules\Invoice\Dtos\AddInvoiceCollectionSubmissionByUserResponseDto;
use Sanf\Core\Modules\Invoice\Enums\InvoiceCollectionSubmissionStatusEnum;
use Sanf\Core\Modules\Invoice\Events\InvoiceCollectionSubmissionAddedEvent;

final class AddInvoiceCollectionSubmissionByUserService extends InvoiceCollectionSubmissionByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddInvoiceCollectionSubmissionByUserRequestDto $dto
     * @return AddInvoiceCollectionSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $batchNo = nano_id();
        $entities = [];
        foreach ($dto->financingUnits as $financingUnit) {
            $entity = $this->repository->add([
                'xid' => nano_id(),
                'profile_xid' => $dto->profileXid,
                'batch_no' => $batchNo,
                'status_id' => InvoiceCollectionSubmissionStatusEnum::PROCESSED,
                'user_id' => $dto->userId,
                'pickup_date' => $dto->pickupDate,
                'contract_no' => $financingUnit->contractNo,
                'serial_no' => $financingUnit->serialNo,
                'year' => $financingUnit->year,
                'brand_type_model' => $financingUnit->brandTypeModel,
            ]);

            $entity->user = $user;
            $entities[] = $entity;
        }

        if ($entities) {
            event(new InvoiceCollectionSubmissionAddedEvent($entities));
        }

        return new AddInvoiceCollectionSubmissionByUserResponseDto([
            'submissions' => $entities
        ]);
    }
}
