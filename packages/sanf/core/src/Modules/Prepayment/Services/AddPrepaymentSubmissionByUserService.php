<?php

namespace Sanf\Core\Modules\Prepayment\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Prepayment\Dtos\AddPrepaymentSubmissionByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Dtos\AddPrepaymentSubmissionByUserResponseDto;
use Sanf\Core\Modules\Prepayment\Enums\PrepaymentStatusEnum;
use Sanf\Core\Modules\Prepayment\Events\PrepaymentSubmissionAddedEvent;

final class AddPrepaymentSubmissionByUserService extends PrepaymentSubmissionByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddPrepaymentSubmissionByUserRequestDto $dto
     * @return AddPrepaymentSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);

        //TODO VALIDATE ALREADY SUBMITTED

        $prepaymentSubmission = $this->repository->add([
            'xid' => nano_id(),
            'status_id' => PrepaymentStatusEnum::PROCESSED,
            'user_id' => $dto->userId,
            'profile_xid' => $dto->profileXid,
            'contract_no' => $dto->prepaymentSimulation->contractNo,
            'prepayment_date' => $dto->prepaymentSimulation->prepaymentDate,
            'total_prepayment' => $dto->prepaymentSimulation->totalPrepayment,
            'currency_type' => $dto->prepaymentSimulation->currencyType,
            'items' => $dto->prepaymentSimulation->items,
        ]);

        $prepaymentSubmission->user = $user;
        $prepaymentSubmission->prepayment_date = CarbonImmutable::make($prepaymentSubmission->prepayment_date);
        event(new PrepaymentSubmissionAddedEvent($prepaymentSubmission));

        return new AddPrepaymentSubmissionByUserResponseDto([
            'xid' => $prepaymentSubmission->xid,
            'statusId' => $prepaymentSubmission->status_id,
            'userId' => $prepaymentSubmission->user_id,
            'profileXid' => $prepaymentSubmission->profile_xid,
            'contractNo' => $prepaymentSubmission->contract_no,
            'prepaymentDate' => $prepaymentSubmission->prepayment_date,
            'totalPrepayment' => $prepaymentSubmission->total_prepayment,
            'currencyType' => $prepaymentSubmission->currency_type,
            'items' => $prepaymentSubmission->items,
        ]);
    }
}
