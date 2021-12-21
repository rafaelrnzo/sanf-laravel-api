<?php

namespace Sanf\Core\Modules\Insurance\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Insurance\Dtos\ReadInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Dtos\ReadInsuranceClaimSubmissionByUserResponseDto;
use Sanf\Core\Modules\Insurance\Exceptions\InsuranceClaimSubmissionNotFoundException;

final class ReadInsuranceClaimSubmissionByUserService extends InsuranceClaimSubmissionByUserService implements ApplicationServiceInterface
{
    /**
     * @param ReadInsuranceClaimSubmissionByUserRequestDto $dto
     * @return ReadInsuranceClaimSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $entity = $this->repository->findByXid($dto->xid);
        if (is_null($entity)) {
            throw new InsuranceClaimSubmissionNotFoundException();
        }
        //TODO VALIDATE USER & OWNERSHIP

        return new ReadInsuranceClaimSubmissionByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
            'userId' => $entity->user_id,
            'status' => $entity->status,
            //TODO HERE
            'createdAt' => CarbonImmutable::make($entity->created_at),
            'updatedAt' => CarbonImmutable::make($entity->updated_at),
        ]);
    }
}
