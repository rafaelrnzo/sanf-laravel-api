<?php

namespace Sanf\Core\Modules\User\Services;

use function collect;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

/**
 * Class SwitchActiveCustomerProfileService.
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
    public function __construct(UserRepositoryInterface $repository, SanfCoreApiClient $internalApiClient) //TODO REPOSITORY
    {
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto = null)
    {
        $user = $this->repository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $profiles = $this->internalApiClient->findCustomerById($dto->customerId);
        $profile = collect($profiles['data'])->first();

        $xid = $profile['CUST_ID_SANF'];
        $profile_type = $profile['ID_IDENTITY'];
        $full_name = $user->full_name;
        $company_name = $user->company_name;

        if ($profile['ID_IDENTITY'] === ProfileType::PERSONAL) {
            $full_name = $profile['IDENTITY_NAME'];
        } elseif ($profile['ID_IDENTITY'] === ProfileType::COMPANY) {
            $company_name = $profile['IDENTITY_NAME'];
        } else {
            throw new \Exception("invalid profile type {$profile['ID_IDENTITY']}");
        }

        $this->repository->update([
            'xid' => $xid,
            'profile_type' => $profile_type,
            'full_name' => $full_name,
            'company_name' => $company_name,
        ], $user->id);
    }
}
