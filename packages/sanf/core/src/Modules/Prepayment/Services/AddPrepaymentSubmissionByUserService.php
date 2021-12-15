<?php

namespace Sanf\Core\Modules\Prepayment\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Prepayment\Dtos\AddPrepaymentSubmissionByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Dtos\AddPrepaymentSubmissionByUserResponseDto;
use Sanf\Core\Modules\Prepayment\Enums\PrepaymentStatusEnum;
use Sanf\Core\Modules\Prepayment\Repositories\PrepaymentSubmissionRepositoryInterface;

final class AddPrepaymentSubmissionByUserService implements ApplicationServiceInterface
{
    protected PrepaymentSubmissionRepositoryInterface $repository;

    /**
     * AddPrepaymentSubmissionByUserService constructor.
     * @param PrepaymentSubmissionRepositoryInterface $repository
     */
    public function __construct(PrepaymentSubmissionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param AddPrepaymentSubmissionByUserRequestDto $dto
     * @return AddPrepaymentSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        //TODO VALIDATE USER
        //TODO VALIDATE PROFILE
        //TODO VALIDATE ALREADY SUBMITTED

        $data = $this->repository->add([
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

        return new AddPrepaymentSubmissionByUserResponseDto([
            'xid' => $data->xid,
        ]);
    }
}
