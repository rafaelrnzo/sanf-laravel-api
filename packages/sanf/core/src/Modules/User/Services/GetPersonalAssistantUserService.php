<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetPersonalAssistantUserService extends UserService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $internalApiClient;

    public function __construct(UserRepositoryInterface $userRepository, SanfCoreApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto = null)
    {
        $user = $this->userRepository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $response = $this->internalApiClient->findCustomerById($dto->profileActiveId);
        $profile = collect($response['data'])
            ->map(function ($item) {
                $contract = $item['F_KONTRAK'] ?? null;
                $hasContract = !is_null($contract);
                if ((string) $contract == 0) {
                    $hasContract = false;
                }

                return (object) [
                    'msisdn' => $item['NO_AE'],
                    'cust_id' => $item['CUST_ID_SANF'],
                    'has_contract' => $hasContract,
                ];
            })->where('cust_id', $dto->profileActiveId)
            ->first();

        if (!$profile) {
            throw new UserNotFoundException('Theres no user personal');
        }

        $profile->message = ''; //TODO CONFIGURABLE MESSAGE

        return $profile;
    }
}
