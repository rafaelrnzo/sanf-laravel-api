<?php

namespace Sanf\Core\Modules\Plafond\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Dtos\AddPlafondRequestDto;

final class ApplyPlafondByUserService extends PlafondByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddPlafondRequestDto $dto
     * @return mixed|void
     */
    public function execute($dto = null)
    {
        $this->repository->submitApplication($dto->profileXid, $dto->plafondTypeId, $dto->amount);
    }
}
