<?php

namespace Sanf\Core\Modules\Plafond\Services;


use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\AddPlafondRequestDto;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;
use Sanf\Core\Modules\Plafond\Events\PlafondRequestedEvent;

final class ApplyNewPlafondByUserService extends PlafondByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddPlafondRequestDto $dto
     * @return mixed|void
     */
    public function execute($dto = null)
    {
        $this->repository->submitApplication($dto->profileXid, $dto->typeId, $dto->amount);
        $plafondRequest = (object)[
            'amount' => $dto->amount,
            'createdAt' => Carbon::now(),
            'type' => ($dto->typeId == PlafondTypeEnum::UNIT) ? 'Unit' : 'Sparepart',
        ];
        event(new PlafondRequestedEvent($plafondRequest, $dto->profile));
    }
}
