<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\ActivateUserServiceInterface;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class ActivateUserAndRegisterInternalService implements ActivateUserServiceInterface
{
    protected $service;

    protected $repository;

    protected $internalApiClient;

    protected $transactionalSession;

    /**
     * RegisterByEmailService constructor.
     * @param $jwt
     */
    public function __construct(
        ActivateUserServiceInterface $service,
        UserRepositoryInterface $repository,
        SanfCoreApiClient $internalApiClient,
        TransactionalSessionInterface $transactionalSession
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
        $this->transactionalSession = $transactionalSession;
    }

    public function execute($dto = null)
    {
        $sodiumQuery = SodiumEncryption::query();

        $sodiumQuery->beginTransaction();

        try {
            $user = $this->service->execute($dto);

            try {
                $this->internalApiClient->registerPersonal(
                    $user->full_name,
                    $user->username,
                    $user->landline_number,
                    $user->phone_number,
                );
            } catch (\Exception $exception) {
                report($exception);
            }

            $profiles = $this->internalApiClient->findCustomerByEmail($user->username);
            $profile = (collect($profiles['data'])->where('ID_IDENTITY', ProfileType::PERSONAL)->first());
            $customerId = $profile['CUST_ID_SANF'];

            $this->repository->update([
                'profile_type' => ProfileType::PERSONAL,
                'xid' => $customerId,
                'personal_xid' => $customerId,
            ], $user->id);

            /** @var \Sanf\Core\Modules\User\AuthEncryptedModel $user */
            $user = $this->repository->findById($user->id);

            $sodiumQuery->commit();

            //TODO DTO
            return json_decode(json_encode($user));
        } catch (\Throwable $th) {
            $sodiumQuery->rollBack();
            report($th);
            throw $th;
        }
    }
}
