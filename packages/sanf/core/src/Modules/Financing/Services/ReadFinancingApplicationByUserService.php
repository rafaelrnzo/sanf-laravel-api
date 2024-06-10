<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Exceptions\FinancingApplicationInvalidException;

class ReadFinancingApplicationByUserService extends FinancingByUserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);

        $financingApplication = $this->financingApplicationRepository->findByXid($user->id, $dto->xid, $dto->applicationXid);

        if (is_null($financingApplication)) {
            throw new FinancingApplicationInvalidException('Financing Application Not Found');
        }

        return $financingApplication;
    }
}
