<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\PdcHold\Dtos\ResumePdcByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\ResumePdcByUserResponseDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Exceptions\PdcHoldGiroNotFoundException;
use Sanf\Core\Modules\PdcHold\Models\PdcHoldGiroModel;
use Sanf\Core\Modules\PdcHold\Models\PdcHoldModel;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldGiroRepositoryInterface;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldRepositoryInterface;

/**
 * @since CR2025
 */
final class ResumePdcByUserService implements ApplicationServiceInterface
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
     * @param ResumePdcByUserRequestDto $dto
     * @return ResumePdcByUserResponseDto
     */
    public function execute($dto = null)
    {
        $entity = DB::transaction(function () use ($dto) {
            $pdcHoldXid = app()->make('nanoid')->formatedId(PdcHoldModel::XID_ALPHABET, (int) config('additional.pdc_hold.xid_length', PdcHoldModel::XID_LENGTH));
            $status = new PdcHoldStatusEnum(PdcHoldStatusEnum::PROCESSED);

            $holdGiros = $this->pdcHoldGiroRepository->findToResume($dto->giroXids, $dto->profileXid);

            if ($holdGiros->isEmpty()) {
                throw new PdcHoldGiroNotFoundException();
            }

            $entity = $this->pdcHoldRepository->add([
                'xid' => $pdcHoldXid,
                'user_id' => $dto->userId,
                'customer_id' => $dto->profileXid,
                'type' => PdcHoldTypeEnum::RESUME,
                'date_start' => $holdGiros->min('pdc_hold.date_start'),
                'date_end' => $holdGiros->max('pdc_hold.date_end'),
                'status_id' => $status->getValue(),
                'reason_id' => null,
                'reason_value' => null,
                'status' => $status->getLabel(),
            ]);

            $updatedGiroXids = [];

            /** @var PdcHoldGiroModel $holdgiro */
            foreach ($holdGiros as $holdgiro) {
                $resumeGiro = $this->pdcHoldGiroRepository->add([
                    'xid' => nano_id(),
                    'pdc_hold_id' => $holdgiro->pdc_hold_id,
                    'pdc_resume_id' => $entity->id,
                    'customer_id' => $holdgiro->customer_id,
                    'contract_no' => $holdgiro->contract_no,
                    'pdc_no' => $holdgiro->pdc_no,
                    'amount' => $holdgiro->amount,
                    'currency_type' => $holdgiro->currency_type,
                    'giro_date' => $holdgiro->giro_date,
                    'pdc_type' => $holdgiro->pdc_type,
                ]);

                $updatedGiroXids[$holdgiro->xid] = $resumeGiro->xid;
            }

            $entity->updated_giro_xids = $updatedGiroXids;

            return $entity;
        });

        return new ResumePdcByUserResponseDto([
            'xid' => $entity->xid,
            'giroXids' => $entity->updated_giro_xids,
        ]);
    }
}
