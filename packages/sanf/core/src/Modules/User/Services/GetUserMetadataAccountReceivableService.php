<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetUserMetadataAccountReceivableService extends UserService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, SanfCoreApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto = null)
    {
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $response = $this->internalApiClient->getMetadataContract($dto->profile_xid);
        $collect = collect($response->data);
        $totalOutstandingAmount = 0;
        $totalPaidAmount = 0;

        $totalOutstandingAmount += $collect->where('CURR_ID_AKTIF', '=', $dto->currency_type)->sum('AMT_AKTIF');
        $totalPaidAmount += $collect->where('CURR_ID_SELESAI', '=', $dto->currency_type)->sum('AMT_SELESAI');

        return (object) [
            'total_outstanding_amount' => $totalOutstandingAmount,
            'total_paid_amount' => $totalPaidAmount,
            'currency_type' => $dto->currency_type,
        ];
    }
}
