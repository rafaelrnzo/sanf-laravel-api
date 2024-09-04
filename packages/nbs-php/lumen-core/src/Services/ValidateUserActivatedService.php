<?php

namespace NbsPhp\Core\Services;

use NbsPhp\Core\Exceptions\UserActivationFailedException;
use NbsPhp\Core\Exceptions\UserAlreadyActivatedException;
use Sanf\Core\Modules\User\AuthEncryptedModel;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class ValidateUserActivatedService implements ActivateUserServiceInterface
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

    /**
     * @param $dto
     * @return AuthEncryptedModel
     * @throws UserActivationFailedException
     * @throws UserAlreadyActivatedException
     */
    public function execute($dto = null)
    {
        /** @var AuthEncryptedModel $user */
        $user = $this->repository->findById($dto->userId);
        if (!$user) {
            throw new UserActivationFailedException('user activation: not found');
        }
        if (!hash_equals((string) $dto->token, hash('sha256', $user->getEmailForActivation()))) {
            throw new UserActivationFailedException('user activation: invalid token');
        }
        if (!$user->needActivation()) {
            throw new UserAlreadyActivatedException('user activation: already activated');
        }

        return $user;
    }
}
