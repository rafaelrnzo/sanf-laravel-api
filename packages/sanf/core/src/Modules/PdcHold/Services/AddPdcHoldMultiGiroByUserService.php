<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\PdcHold\Dtos\AddPdcHoldByUserResponseDto;
use Sanf\Core\Modules\PdcHold\Dtos\AddPdcHoldMultiGiroByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\PdcHoldMultiGiroRequestDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Events\PdcHoldMultiGiroSubmittedEvent;
use Sanf\Core\Modules\PdcHold\Models\PdcHoldModel;

/**
 * @since CR2025
 */
final class AddPdcHoldMultiGiroByUserService extends PdcHoldByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddPdcHoldMultiGiroByUserRequestDto $dto
     * @return AddPdcHoldByUserResponseDto
     */
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);

        $entity = DB::transaction(function () use ($dto) {
            $pdcHoldXid = app()->make('nanoid')->formatedId(PdcHoldModel::XID_ALPHABET, config('additional.pdc_hold.xid_length'));
            $status = new PdcHoldStatusEnum(PdcHoldStatusEnum::PROCESSED);

            $entity = $this->pdcHoldRepository->add([
                'xid' => $pdcHoldXid,
                'user_id' => $dto->userId,
                'customer_id' => $dto->profileXid,
                'type' => PdcHoldTypeEnum::MULTI_GIRO,
                'date_start' => $dto->dateStart,
                'date_end' => $dto->dateEnd,
                'reason_id' => $dto->reason->id,
                'reason_value' => $dto->reason->value,
                'status_id' => $status->getValue(),
                'status' => $status->getLabel(),
            ]);

            $giros = [];
            /** @var PdcHoldMultiGiroRequestDto $giro */
            foreach ($dto->multiGiro as $giro) {
                $addedGiro = $this->pdcHoldGiroRepository->add([
                    'xid' => nano_id(),
                    'pdc_hold_id' => $entity->id,
                    'pdc_resume_id' => null,
                    'customer_id' => $dto->profileXid,
                    'contract_no' => $dto->contractNo,
                    'pdc_no' => $giro->pdcNo,
                    'amount' => $giro->amount,
                    'currency_type' => $giro->currencyType,
                    'giro_date' => $giro->date,
                    'pdc_type' => $giro->pdcType,
                ]);

                $giros[] = (object) [
                    'pdc_no' => $addedGiro->pdc_no,
                    'amount' => $addedGiro->amount,
                    'giro_date' => $addedGiro->giro_date,
                    'pdc_type' => $addedGiro->pdc_type,
                    'currency_type' => $addedGiro->currency_type,
                ];
            }

            $entity->contract_no = $dto->contractNo;
            $entity->giros = $giros;

            return $entity;
        });

        if ($entity) {
            event(new PdcHoldMultiGiroSubmittedEvent($entity, $user));
        }

        return new AddPdcHoldByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
        ]);
    }
}
