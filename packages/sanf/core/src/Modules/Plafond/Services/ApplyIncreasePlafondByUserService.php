<?php

namespace Sanf\Core\Modules\Plafond\Services;


use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\AddPlafondRequestDto;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;
use Sanf\Core\Modules\Plafond\Events\PlafondIncreaseRequestedEvent;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondInvalidException;

final class ApplyIncreasePlafondByUserService extends PlafondByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddPlafondRequestDto $dto
     * @return mixed|void
     */
    public function execute($dto = null)
    {
        $this->repository->submitApplication($dto->profileXid, $dto->typeId, $dto->amount);
        $plafond = $this->repository->getByProfileAndType($dto->profileXid, $dto->typeId);
        if (is_null($plafond)) {
            throw new PlafondInvalidException('Plafond Not Found');
        }

        switch ($dto->typeId) {
            case PlafondTypeEnum::UNIT:
                $plafondType = __('Unit');
                break;
            case PlafondTypeEnum::SPAREPART:
                $plafondType = __('Sparepart');
                break;
            case PlafondTypeEnum::FACTORING:
            default:
                $plafondType = __('Factoring');
                break;
        }

        $plafondRequest = (object)[
            'currentBalance' => $plafond->getCurrentBalance(),
            'addedBalance' => $dto->amount - $plafond->getCurrentBalance(),
            'submittedBalance' => $dto->amount,
            'createdAt' => Carbon::now(),
            'type' => $plafondType,
        ];
        event(new PlafondIncreaseRequestedEvent($plafondRequest, $dto->profile));
    }
}
