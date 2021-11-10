<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Services\RegisterByAppleServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Integration\InternalApiClient;

class RegisterInternalByAppleService implements RegisterByAppleServiceInterface
{
    protected $service;

    protected $repository;

    protected $internalApiClient;

    /**
     * RegisterByEmailService constructor.
     * @param $jwt
     */
    //TODO USE REPOSITORY
    public function __construct(RegisterByAppleServiceInterface $service, AuthModel $repository, InternalApiClient $internalApiClient)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
    }


    public function execute($dto = null)
    {
        $user = $this->service->execute($dto);
        if ($user->status_id !== UserStatus::ACTIVE) {
            return json_decode(json_encode($user));
        }

        $token = optional($user)->token;
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

        $profiles = $this->internalApiClient->findCustomerByEmail($dto->email);
        $profile = (collect($profiles['data'])->where('ID_IDENTITY', ProfileType::PERSONAL)->first());
        $customerId = $profile['CUST_ID_SANF'];

        /** @var AuthModel $user */
        $user = $this->repository->newQuery()->find($user->id);
        $user->update([
            'profile_type' => ProfileType::PERSONAL,
            'xid' => $customerId,
            'personal_xid' => $customerId
        ]);
        $user->token = $token;

        //TODO DTO
        return json_decode(json_encode($user));
    }
}
