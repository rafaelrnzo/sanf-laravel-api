<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\PdcHold\Dtos\ReadPdcHoldSubmissionByUserResponseDto;
use Sanf\Core\Modules\PdcHold\Dtos\UpdateStatusPdcSubmissionByCoreRequestDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Exceptions\PdcHoldGiroNotFoundException;
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
            $status_id = optional($status)->getValue();

            $pdcHoldSubmission = $this->pdcHoldRepository->findByXid($dto->pdcHoldXid);

            if (!$pdcHoldSubmission) {
                throw new PdcHoldNotFoundException();
            }

            $statusUpdated = $this->pdcHoldRepository->updateStatus($dto->pdcHoldXid, $status);

            if ($status_id == PdcHoldStatusEnum::ACCEPTED) {
                if ($pdcHoldSubmission->type == PdcHoldTypeEnum::RESUME) {
                    if (empty($pdcHoldSubmission->resume_giros)) {
                        throw new PdcHoldGiroNotFoundException();
                    }

                    foreach ($pdcHoldSubmission->resume_giros as $giroToResume)
                    {
                        // delete all hold giros that was submitted to hold
                        $this->pdcHoldGiroRepository->deletePastHolds($pdcHoldSubmission->id, $pdcHoldSubmission->customer_id, $giroToResume->contract_no, $giroToResume->pdc_no);
                    }
                } else {
                    // Collect common contract no & pdc_no first
                    $pendingGiros = collect($pdcHoldSubmission->giros)
                        ->unique(fn ($item) => $item->contract_no . '|' . $item->pdc_no)
                        ->all();

                    if (empty($pendingGiros)) {
                        throw new PdcHoldGiroNotFoundException();
                    }

                    // delete all pending hold giro
                    foreach ($pendingGiros as $pendingGiro)
                    {
                        $this->pdcHoldGiroRepository->deletePendingHolds($pdcHoldSubmission->id, $pdcHoldSubmission->customer_id, $pendingGiro->contract_no, $pendingGiro->pdc_no);
                    }
                    // @TODO: Delete orphan submissions (i.e. pdc holds with no giros)
                }
            }

            if ($statusUpdated) {
                $pdcHoldSubmission->status_id = $status->getValue();
                $pdcHoldSubmission->status = $status->getLabel();
            }

            return $pdcHoldSubmission;
        });

        $pdcType = new PdcHoldTypeEnum($entity->type);
        $giros = $entity->type === PdcHoldTypeEnum::RESUME ? $entity->resume_giros : $entity->giros;

        // @TODO: Send notification to mobile

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
            'giros' => $giros,
            'createdAt' => CarbonImmutable::make($entity->created_at),
            'updatedAt' => CarbonImmutable::make($entity->updated_at),
        ]);
    }
}
