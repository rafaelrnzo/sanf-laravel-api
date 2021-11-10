<?php

namespace Sanf\Core\Modules\Plafond\Services;


use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\AddPlafondRequestDto;
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
        $this->repository->submitApplication($dto->profileXid, $dto->plafondTypeId, $dto->amount);
        $plafond = $this->repository->getByProfileAndType($dto->profileXid, $dto->plafondTypeId);
        if (is_null($plafond)) {
            throw new PlafondInvalidException('Plafond Not Found');
        }
        $plafondRequest = (object)[
            'currentBalance' => $plafond->getCurrentBalance(),
            'addedBalance' => $dto->amount,
            'submittedBalance' => (int)$dto->amount + (int)$plafond->getCurrentBalance(),
            'createdAt' => Carbon::now()
        ];
        event(new PlafondIncreaseRequestedEvent($plafondRequest, $dto->profile));
    }
}
