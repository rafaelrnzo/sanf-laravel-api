<?php


namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\SendEmailFinancingSimulationDto;
use Sanf\Core\Modules\Financing\SendEmailFinancingSimulationJob;

class SendEmailFinancingSimulationService extends FinancingByUserService implements ApplicationServiceInterface
{
    /**
     * @param SendEmailFinancingSimulationDto $dto
     * @return mixed|void
     * @throws \NbsPhp\Core\Exceptions\UserNotFoundException
     */
    public function execute($dto = null)
    {
        // Get user from repository user
        $user = $this->findUserOrFail($dto->user_id);

        // Execute job
        dispatch(new SendEmailFinancingSimulationJob($dto, (object)['name' => $user->full_name, 'email' => $user->username]));
    }
}
