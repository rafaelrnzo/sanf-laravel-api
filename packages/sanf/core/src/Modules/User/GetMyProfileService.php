<?php


namespace Sanf\Core\Modules\User;


use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\InternalApiClient;

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

    public function execute($dto)
    {
        $user = AuthModel::findOrFail($dto->userId);
        $profile = $this->internalApiClient->findCustomerById($user->xid);
        //TODO TIDY UP ENTITY
        $user->profile = (object)[
            'isPic' => (bool)$profile['data'][0]['PIC'],
            'companyName' => $profile['data'][0]['IDENTITY_NAME']
        ];
        //TODO DTO
        return json_decode(json_encode($user));
    }
}
