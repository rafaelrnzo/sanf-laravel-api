<?php

namespace Sanf\Core\Modules\User\Services;

use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\InternalApiClient;

class GetUserMetadataContractService extends UserService implements ApplicationServiceInterface
{

    protected InternalApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, InternalApiClient $internalApiClient)
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
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $response = $this->internalApiClient->getMetadataContract($user->personal_xid);
        $collect = collect($response['data']);
        $totalActive = 0;
        $totalFinish = 0;

        $totalActive += $collect->sum('TOTAL_AKTIF');
        $totalFinish += $collect->sum('TOTAL_SELESAI');

        return (object)[
            'total_active_contract' => $totalActive,
            'total_finished_contract' => $totalFinish,
        ];
    }
}