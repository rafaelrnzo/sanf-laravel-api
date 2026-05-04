<?php

namespace Sanf\Core\Modules\User\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\ForbiddenException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetUserMetadataContractService extends UserService implements ApplicationServiceInterface
{
    protected SanfCoreApiClient $internalApiClient;

    public function __construct(UserRepositoryInterface $userRepository, SanfCoreApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param null $dto
     * @return object
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->findById($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $response = $this->internalApiClient->getMetadataContract($dto->profile_xid);
        
        $profile = $this->internalApiClient->findCustomerById($dto->profile_xid);
        if ($profile['data'][0]['EMAIL_ADDR'] !== $user->username) {
            throw new ForbiddenException('Missmatch User Access');
        }

        $collect = collect($response->data);
        $totalActive = 0;
        $totalFinish = 0;

        $totalActive += $collect->sum('TOTAL_AKTIF');
        $totalFinish += $collect->sum('TOTAL_SELESAI');

        return (object) [
            'total_active_contract' => $totalActive,
            'total_finished_contract' => $totalFinish,
        ];
    }
}
