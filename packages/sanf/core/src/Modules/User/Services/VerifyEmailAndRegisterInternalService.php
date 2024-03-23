<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\VerifyEmailServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class VerifyEmailAndRegisterInternalService implements VerifyEmailServiceInterface
{
    protected $service;

    protected $repository;

    protected $internalApiClient;

    protected $transactionalSession;

    /**
     * RegisterByEmailService constructor.
     * @param $jwt
     */
    //TODO USE REPOSITORY
    public function __construct(
        VerifyEmailServiceInterface $service,
        AuthModel $repository,
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
        $operation = function () use ($dto) {
            $user = $this->service->execute($dto);

            $userCoreAccount = null;
            try {
                $userCoreAccount = $this->internalApiClient->findCustomerByEmail($user->username);
            } catch (SanfInternalApiDataNotFoundException $e) {
                report($e);
            }

            if (is_null($userCoreAccount)) {
                $this->internalApiClient->registerPersonal(
                    $user->full_name,
                    $user->username,
                    $user->landline_number,
                    $user->phone_number,
                );

                $userCoreAccount = $this->internalApiClient->findCustomerByEmail($user->username);
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

            //TODO DTO
            return json_decode(json_encode($user));
        };

        return $this->transactionalSession->executeAtomically(
            $operation->bindTo($this)
        );
    }
}
