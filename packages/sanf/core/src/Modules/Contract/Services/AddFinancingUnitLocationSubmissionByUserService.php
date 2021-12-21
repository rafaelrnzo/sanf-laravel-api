<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dtos\AddFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Dtos\AddFinancingUnitLocationSubmissionByUserResponseDto;
use Sanf\Core\Modules\Contract\Enums\FinancingUnitLocationSubmissionStatusEnum;
use Sanf\Core\Modules\Contract\Events\FinancingUnitLocationSubmissionAddedEvent;

final class AddFinancingUnitLocationSubmissionByUserService extends FinancingUnitLocationSubmissionByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddFinancingUnitLocationSubmissionByUserRequestDto $dto
     * @return AddFinancingUnitLocationSubmissionByUserResponseDto
     */
    public function execute($dto = null)
    {
        //TODO VALIDATE USER & OWNERSHIP

        $entity = $this->repository->add([
            'xid' => nano_id(),
            'status_id' => FinancingUnitLocationSubmissionStatusEnum::PROCESSED,
            'user_id' => $dto->userId,
            //TODO MORE FIELD HERE
        ]);

        event(new FinancingUnitLocationSubmissionAddedEvent($entity));

        return new AddFinancingUnitLocationSubmissionByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
        ]);
    }
}
