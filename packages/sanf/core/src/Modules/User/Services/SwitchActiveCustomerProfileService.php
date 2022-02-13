<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Integration\InternalApiClient;
use function collect;

/**
 * Class SwitchActiveCustomerProfileService
 * @package Sanf\Core\Modules\User\Services
 * @deprecated Active Profile Not Persisted On Backend Anymore
 */
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

    public function execute($dto = null)
    {
        $user = $this->repository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $profiles = $this->internalApiClient->findCustomerById($dto->customerId);
        $profile = collect($profiles['data'])->first();

        //TODO REPO
        $user->xid = $profile['CUST_ID_SANF'];
        $user->profile_type = $profile['ID_IDENTITY'];
        if ($profile['ID_IDENTITY'] === ProfileType::PERSONAL) {
            $user->full_name = $profile['IDENTITY_NAME'];
        } elseif ($profile['ID_IDENTITY'] === ProfileType::COMPANY) {
            $user->company_name = $profile['IDENTITY_NAME'];
        } else {
            throw new \Exception("invalid profile type {$profile['ID_IDENTITY']}");
        }

        $user->save();
    }
}
