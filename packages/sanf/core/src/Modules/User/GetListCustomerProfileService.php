<?php


namespace Sanf\Core\Modules\User;


use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\InternalApiClient;

class GetListCustomerProfileService implements ApplicationServiceInterface
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
        $user = $this->repository->newQuery()->where('username', $dto->email)->first();
        if (!$user) {
            throw new UserNotFoundException();
        }
        $response = $this->internalApiClient->findCustomerByEmail($dto->email);

        return collect($response['data'])
            ->map(function ($item) use ($user) {
                return (object)[
                    "xid" => $item['CUST_ID_SANF'],
                    "typeId" => $item['ID_IDENTITY'],
                    "typeName" => $item['DESC_IDENTITY'],
                    "fullName" => $item['IDENTITY_NAME'],
                    "email" => $item['EMAIL_ADDR'],
                    "isActive" => ($user->xid === $item['CUST_ID_SANF']),
                    "isPic" => (bool)$item['PIC']
                ];
            });
    }
}
