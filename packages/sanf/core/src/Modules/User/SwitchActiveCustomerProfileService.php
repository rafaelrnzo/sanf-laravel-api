<?php


namespace Sanf\Core\Modules\User;


use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\InternalApiClient;

class SwitchActiveCustomerProfileService implements ApplicationServiceInterface
{
    protected $repository;
    protected $internalApiClient;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(AuthModel $repository, InternalApiClient $internalApiClient) //TODO REPOSITORY
    {
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto)
    {
        $user = $this->repository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $response = $this->internalApiClient->findCustomerById($dto->customerId);

        //TODO REPO
        $user->xid = $response['data'][0]['CUST_ID_SANF'];
        $user->profile_type = $response['data'][0]['ID_IDENTITY'];
        $user->save();
    }
}
