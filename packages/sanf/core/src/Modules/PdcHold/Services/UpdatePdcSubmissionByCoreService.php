<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\PdcHold\Dtos\ReadPdcHoldSubmissionByUserResponseDto;
use Sanf\Core\Modules\PdcHold\Dtos\UpdateStatusPdcSubmissionByCoreRequestDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Events\PdcHoldSubmissionUpdateByCoreNotificationEvent;
use Sanf\Core\Modules\PdcHold\Exceptions\PdcHoldNotFoundException;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldGiroRepositoryInterface;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldRepositoryInterface;

/**
 * @since CR2025
 */
final class UpdatePdcSubmissionByCoreService implements ApplicationServiceInterface
{
    protected PdcHoldRepositoryInterface $pdcHoldRepository;
    protected PdcHoldGiroRepositoryInterface $pdcHoldGiroRepository;

    public function __construct(
        PdcHoldRepositoryInterface $pdcHoldRepository,
        PdcHoldGiroRepositoryInterface $pdcHoldGiroRepository
    ) {
        $this->pdcHoldRepository = $pdcHoldRepository;
        $this->pdcHoldGiroRepository = $pdcHoldGiroRepository;
    }

    /**
     * @param UpdateStatusPdcSubmissionByCoreRequestDto $dto
     * @return ReadPdcHoldSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $entity = DB::transaction(function () use ($dto) {
            $status = PdcHoldStatusEnum::fromCore($dto->status);

            $pdcHoldSubmission = $this->pdcHoldRepository->findByXid($dto->pdcHoldXid);

            if (!$pdcHoldSubmission) {
                throw new PdcHoldNotFoundException();
            }

            $statusUpdated = $this->pdcHoldRepository->updateStatus($dto->pdcHoldXid, $status);

            if ($statusUpdated) {
                $pdcHoldSubmission->status_id = $status->getValue();
                $pdcHoldSubmission->status = $status->getLabel();
            }

            return $pdcHoldSubmission;
        });

        $pdcType = new PdcHoldTypeEnum($entity->type);
        $giros = $entity->type === PdcHoldTypeEnum::RESUME ? $entity->resume_giros : $entity->giros_no_resume;

        $notificationContent = (object) [
            'userId' => $entity->user_id ?? null,
            'customerId' => $entity->customer_id,
            'type' => $entity->type,
            'pdcHoldXid' => $entity->xid,
            'statusId' => $entity->status_id,
        ];

        event(new PdcHoldSubmissionUpdateByCoreNotificationEvent($notificationContent));

        return new ReadPdcHoldSubmissionByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
            'status'  => (object) [
                'id' => $entity->status_id,
                'name' => $entity->status,
            ],
            'type' => $pdcType,
            'dateStart' => CarbonImmutable::make($entity->date_start),
            'dateEnd' => CarbonImmutable::make($entity->date_end),
            'reasonValue' => $entity->reason_value,
            'giros' => $giros,
            'createdAt' => CarbonImmutable::make($entity->created_at),
            'updatedAt' => CarbonImmutable::make($entity->updated_at),
        ]);
    }
}
