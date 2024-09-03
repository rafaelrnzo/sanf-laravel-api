<?php

namespace NbsPhp\Core\Services;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use NbsPhp\Core\Exceptions\VerifyEmailFailedException;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class SendEmailVerificationService implements ApplicationServiceInterface
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
        $user = SodiumEncryption::query()->transaction(
            function () use ($dto) {
                return $this->repository->findByEmail($dto->email);
            }
        );

        if (!$user) {
            throw new VerifyEmailFailedException();
        }

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
    }
}
