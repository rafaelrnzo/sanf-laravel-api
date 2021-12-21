<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dtos\ReadFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Dtos\ReadFinancingUnitLocationSubmissionByUserResponseDto;
use Sanf\Core\Modules\Contract\Exceptions\FinancingUnitLocationSubmissionNotFoundException;

final class ReadFinancingUnitLocationSubmissionByUserService extends FinancingUnitLocationSubmissionByUserService implements ApplicationServiceInterface
{
    /**
     * @param ReadFinancingUnitLocationSubmissionByUserRequestDto $dto
     * @return ReadFinancingUnitLocationSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        $entity = $this->repository->findByXid($dto->xid);
        if (is_null($entity)) {
            throw new FinancingUnitLocationSubmissionNotFoundException();
        }
        //TODO VALIDATE USER & OWNERSHIP

        return new ReadFinancingUnitLocationSubmissionByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
            //TODO HERE
            'userId' => $entity->user_id,
            'status' => $entity->status,
            'createdAt' => CarbonImmutable::make($entity->created_at),
            'updatedAt' => CarbonImmutable::make($entity->updated_at),
        ]);
    }
}
