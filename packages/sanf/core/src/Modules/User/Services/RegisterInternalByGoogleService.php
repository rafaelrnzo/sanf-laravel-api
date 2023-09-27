<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Services\RegisterByGoogleServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

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
    public function __construct(RegisterByGoogleServiceInterface $service, AuthModel $repository, SanfCoreApiClient $internalApiClient)
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

        $userCoreAccount = null;
        try {
            $userCoreAccount = $this->internalApiClient->findCustomerByEmail($dto->email);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            report($exception);
        }

        if (is_null($userCoreAccount)) {
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
        }

        $profile = (collect($userCoreAccount['data'])->where('ID_IDENTITY', ProfileType::PERSONAL)->first());
        $customerId = $profile['CUST_ID_SANF'];

        /** @var AuthModel $user */
        $user = $this->repository->newQuery()->find($user->id);
        $user->update([
            'profile_type' => ProfileType::PERSONAL,
            'xid' => $customerId,
            'personal_xid' => $customerId,
        ]);

        $user->token =  optional($user)->token;

        //TODO DTO
        return json_decode(json_encode($user));
    }
}
