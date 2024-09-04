<?php

namespace NbsPhp\Core\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserActivationFailedException;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class ActivateUserService implements ActivateUserServiceInterface
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
        $user = $this->repository->findById($dto->userId);
        if (!$user) {
            throw new UserActivationFailedException('user activation: not found');
        }
        if (!hash_equals((string) $dto->token, hash('sha256', $user->getEmailForActivation()))) {
            throw new UserActivationFailedException('user activation: invalid token');
        }
        if (!$user->needActivation()) {
            throw new UserActivationFailedException('user activation: already activated');
        }
        if (is_null($user->email_verified_at)) {
            $user->markEmailAsVerified();
        }

        //TODO REPOSITORY
        $user->password = bcrypt($dto->password);
        $user->password_updated_at = Carbon::now();
        $user->save();

        $user->markUserActivated();

        return $user;
    }
}
