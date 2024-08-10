<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\FinancingSimulationPdfDto;
use Sanf\Core\Modules\Financing\Jobs\FinancingSimulationSendEmailForAdminJob;
use Sanf\Core\Modules\Financing\Jobs\FinancingSimulationSendEmailForUserJob;

class FinancingSimulationSendEmailService extends FinancingByUserService implements ApplicationServiceInterface
{
    /**
     * @param FinancingSimulationPdfDto $dto
     * @return mixed|void
     * @throws \NbsPhp\Core\Exceptions\UserNotFoundException
     */
    public function execute($dto = null)
    {
        // Get user from repository user
        $user = $this->findUserOrFail($dto->userId);

        // Execute job
        dispatch(
            new FinancingSimulationSendEmailForUserJob(
                $dto,
                (object) ['name' => $user->full_name, 'email' => $user->username]
            )
        );
        dispatch(
            new FinancingSimulationSendEmailForAdminJob(
                $dto,
                (object) ['name' => $user->full_name, 'email' => explode(',', config('sanf-mobile.mail_to.marketing'))]
            )
        );
    }
}
