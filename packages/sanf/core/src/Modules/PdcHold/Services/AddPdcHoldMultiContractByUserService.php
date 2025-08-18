<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\PdcHold\Dtos\AddPdcHoldByUserResponseDto;
use Sanf\Core\Modules\PdcHold\Dtos\AddPdcHoldMultiContractByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\PdcHoldMultiContractRequestDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Events\PdcHoldMultiContractSubmittedEvent;

/**
 * @since CR2025
 */
final class AddPdcHoldMultiContractByUserService extends PdcHoldByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddPdcHoldMultiContractByUserRequestDto $dto
     * @return AddPdcHoldByUserResponseDto
     */
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);

        $entity = DB::transaction(function () use ($dto) {
            $pdcHoldXid = nano_id();
            $status = new PdcHoldStatusEnum(PdcHoldStatusEnum::PROCESSED);

            $entity = $this->pdcHoldRepository->add([
                'xid' => $pdcHoldXid,
                'user_id' => $dto->userId,
                'customer_id' => $dto->profileXid,
                'type' => PdcHoldTypeEnum::MULTI_CONTRACT,
                'date_start' => $dto->period,
                'date_end' => null,
                'reason_id' => $dto->reason->id,
                'reason_value' => $dto->reason->value,
                'status_id' => $status->getValue(),
                'status' => $status->getLabel(),
            ]);

            $giros_amount = [];
            /** @var PdcHoldMultiContractRequestDto $giro */
            foreach ($dto->multiContract as $giro) {
                $this->pdcHoldGiroRepository->add([
                    'xid' => nano_id(),
                    'pdc_hold_id' => $entity->id,
                    'pdc_resume_id' => null,
                    'customer_id' => $dto->profileXid,
                    'contract_no' => $giro->contractNo,
                    'pdc_no' => $giro->pdcNo,
                    'amount' => $giro->amount,
                    'currency_type' => $giro->currencyType,
                    'giro_date' => $giro->date,
                    'pdc_type' => $giro->pdcType,
                ]);

                $giros_amount[$giro->contractNo] = ($giros_amount[$giro->contractNo] ?? 0) + $giro->amount;
            }

            $entity->giros_amount = $giros_amount;

            return $entity;
        });

        if ($entity) {
            event(new PdcHoldMultiContractSubmittedEvent($entity, $user));
        }

        return new AddPdcHoldByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
        ]);
    }
}
