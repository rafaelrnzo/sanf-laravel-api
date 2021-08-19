<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Services\RegisterByGoogleServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\ProfileType;
use Sanf\Integration\InternalApiClient;

class RegisterInternalByGoogleService implements RegisterByGoogleServiceInterface
{
    protected $service;

    protected $repository;

    protected $internalApiClient;

    /**
     * RegisterByEmailService constructor.
     * @param $jwt
     */
    //TODO USE REPOSITORY
    public function __construct(RegisterByGoogleServiceInterface $service, AuthModel $repository, InternalApiClient $internalApiClient)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
    }


    public function execute($dto)
    {
        $user = $this->service->execute($dto);
        $customerId = null;
        try {
            $this->internalApiClient->registerPersonal(
                $dto->fullName,
                $dto->email,
                $dto->landlineNumber,
                $dto->phoneNumber,
            );
        } catch (\Exception $exception) {
            report($exception);
        }

        try {
            $profiles = $this->internalApiClient->findCustomerByEmail($dto->email);
            $profile = (collect($profiles['data'])->where('ID_IDENTITY', ProfileType::PERSONAL)->first());
            $customerId = $profile['CUST_ID_SANF'];
        } catch (\Exception $exception) {
            report($exception);
        }

        /** @var AuthModel $user */
        $user = $this->repository->newQuery()->find($user->id);
        $user->update([
            'profile_type' => ProfileType::PERSONAL,
            'xid' => $customerId
        ]);

        //TODO DTO
        return json_decode(json_encode($user));
    }
}
