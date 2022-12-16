<?php


namespace NbsPhp\Core\Services;


use NbsPhp\Core\Exceptions\UserActivationFailedException;
use NbsPhp\Core\Exceptions\UserAlreadyActivatedException;
use NbsPhp\Core\Models\AuthModel;

class ValidateUserActivatedService implements ActivateUserServiceInterface
{
    protected $repository;

    /**
     * VerifyEmailService constructor.
     * @param $repository
     */
    public function __construct(AuthModel $repository) //TODO USE REPOSITORY
    {
        $this->repository = $repository;
    }

    /**
     * @param $dto
     * @return AuthModel
     * @throws UserActivationFailedException
     * @throws UserAlreadyActivatedException
     */
    public function execute($dto = null)
    {
        /** @var AuthModel $user */
        $user = $this->repository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserActivationFailedException('user activation: not found');
        }
        if (!hash_equals((string)$dto->token, sha1($user->getEmailForActivation()))) {
            throw new UserActivationFailedException('user activation: invalid token');
        }
        if (!$user->needActivation()) {
            throw new UserAlreadyActivatedException('user activation: already activated');
        }
        return $user;
    }
}
