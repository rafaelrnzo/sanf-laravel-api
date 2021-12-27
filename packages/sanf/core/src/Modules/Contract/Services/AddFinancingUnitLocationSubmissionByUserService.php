<?php

namespace Sanf\Core\Modules\Contract\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
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
     * @throws BindingResolutionException
     * @throws UserNotFoundException
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $entity = $this->repository->findByContractNoAndSerialNo($dto->userId, $dto->xid, $dto->serialNo);

        if (is_null($entity)) {
            $entity = $this->repository->add([
                'xid' => nano_id(),
                'status_id' => FinancingUnitLocationSubmissionStatusEnum::IN_PROGRESS,
                'user_id' => $dto->userId,
                'profile_xid' => $user->personal_xid,
                'contract_no' => $dto->xid,
                'serial_no' => $dto->serialNo,
                'submitted_location_metadata' => json_encode($dto->submittedLocationMetadata),
            ]);
        } else {
            $this->repository->update([
                'id' => $entity->id,
                'status_id' => FinancingUnitLocationSubmissionStatusEnum::IN_PROGRESS,
                'submitted_location_metadata' => json_encode($dto->submittedLocationMetadata),
            ]);

            $entity = $this->repository->findByContractNoAndSerialNo($dto->userId, $dto->xid, $dto->serialNo);
        }

        $entity->submitted_location_metadata = json_decode($entity->submitted_location_metadata);
        $entity->brand_type_model = $dto->brandTypeModel;
        $entity->year = $dto->year;
        $entity->location_metadata = (object)$dto->locationMetadata;
        $entity->user = $user;
        event(new FinancingUnitLocationSubmissionAddedEvent($entity));

        return new AddFinancingUnitLocationSubmissionByUserResponseDto([
            'id' => $entity->id,
            'xid' => $entity->xid,
        ]);
    }
}
