<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Integration\InternalApiClient;
use function collect;

class GetMyProfileService implements ApplicationServiceInterface
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
        $user = AuthModel::findOrFail($dto->userId);
        if (empty($user->xid) || empty($user->personal_xid)) {
            $profiles = $this->internalApiClient->findCustomerByEmail($user->username);
            $profile = (collect($profiles['data'])->where('ID_IDENTITY', ProfileType::PERSONAL)->first());
            $user->xid = $profile['CUST_ID_SANF'];
            $user->personal_xid = $profile['CUST_ID_SANF'];
            $user->profile_type = $profile['ID_IDENTITY'];
            $user->save();
        } else {
            $profiles = $this->internalApiClient->findCustomerById($user->xid);
            $profile = collect($profiles['data'])->first();
        }
        //TODO TIDY UP ENTITY
        $user->profile = (object)[
            'isPic' => (bool)$profile['PIC'],
            'companyName' => $profile['IDENTITY_NAME'],
            'phoneNumber' => $profile['NO_HP']
        ];

        //TODO DTO
        return json_decode(json_encode($user));
    }
}
