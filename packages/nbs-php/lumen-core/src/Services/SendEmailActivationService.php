<?php

namespace NbsPhp\Core\Services;

use NbsPhp\Core\Exceptions\UserActivationFailedException;
use NbsPhp\Core\Models\NeedSetupPasswordInterface;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class SendEmailActivationService implements ApplicationServiceInterface
{
    protected $repository;

    /**
     * VerifyEmailService constructor.
     * @param $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        /** @var \Sanf\Core\Modules\User\AuthEncryptedModel $user */
        $user = SodiumEncryption::query()->transaction(function () use ($dto) {
            return $this->repository->findByEmail($dto->email);
        });

        if (!$user) {
            throw new UserActivationFailedException();
        }

        if ($user instanceof NeedSetupPasswordInterface && $user->needActivation()) {
            $user->sendUserActivationNotification();
        }
    }
}
