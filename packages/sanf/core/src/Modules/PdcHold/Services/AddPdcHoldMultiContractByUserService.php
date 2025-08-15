<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\PdcHold\Dtos\AddPdcHoldByUserResponseDto;
use Sanf\Core\Modules\PdcHold\Dtos\AddPdcHoldMultiContractByUserRequestDto;
use Sanf\Core\Modules\PdcHold\Dtos\PdcHoldMultiContractRequestDto;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldGiroRepositoryInterface;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldRepositoryInterface;

/**
 * @since CR2025
 */
final class AddPdcHoldMultiContractByUserService implements ApplicationServiceInterface
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
     * @param AddPdcHoldMultiContractByUserRequestDto $dto
     * @return AddPdcHoldByUserResponseDto
     */
    public function execute($dto = null)
    {
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

            $giros = [];
            /** @var PdcHoldMultiContractRequestDto $giro */
            foreach ($dto->multiContract as $giro) {
                $giro = $this->pdcHoldGiroRepository->add([
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

                $giros[] = $giro;
            }

            $entity->giros = $giros;

            return $entity;
        });

        if ($entity) {
            // event(new PdcHoldMultiContractSubmittedEvent($entity));
        }

        return new AddPdcHoldByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
        ]);
    }
}
