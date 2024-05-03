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
        $this->repository->submitApplication(
            $dto->profileXid,
            $dto->typeId,
            '101',
            $dto->amount,
            $dto->notes
        );
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
        $plafondRequest = (object) [
            'profileXid' => $dto->profileXid,
            'amount' => $dto->amount,
            'notes' => $dto->notes,
            'createdAt' => Carbon::now(),
            'type' => $plafondType,
        ];
        event(new PlafondRequestedEvent($plafondRequest, $dto->profile));
    }
}
