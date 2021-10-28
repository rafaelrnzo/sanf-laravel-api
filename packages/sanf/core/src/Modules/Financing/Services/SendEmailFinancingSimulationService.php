<?php


namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\SendEmailFinancingSimulationJob;

class SendEmailFinancingSimulationService extends FinancingByUserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        // Get user from repository user
        $user = $this->findUserOrFail($dto->userId);

        // Execute job
        dispatch(new SendEmailFinancingSimulationJob($dto->result, $user));
    }

}
