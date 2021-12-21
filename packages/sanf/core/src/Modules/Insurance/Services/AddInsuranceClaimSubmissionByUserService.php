<?php

namespace Sanf\Core\Modules\Insurance\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Insurance\Dtos\AddInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Dtos\AddInsuranceClaimSubmissionByUserResponseDto;
use Sanf\Core\Modules\Insurance\Enums\InsuranceClaimSubmissionStatusEnum;
use Sanf\Core\Modules\Insurance\Events\InsuranceClaimSubmissionAddedEvent;

final class AddInsuranceClaimSubmissionByUserService extends InsuranceClaimSubmissionByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddInsuranceClaimSubmissionByUserRequestDto $dto
     * @return AddInsuranceClaimSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        //TODO VALIDATE USER & OWNERSHIP

        $entity = $this->repository->add([
            'xid' => nano_id(),
            'status_id' => InsuranceClaimSubmissionStatusEnum::PROCESSED,
            'user_id' => $dto->userId,
            //TODO MORE FIELD HERE
        ]);

        event(new InsuranceClaimSubmissionAddedEvent($entity));

        return new AddInsuranceClaimSubmissionByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
        ]);
    }
}
