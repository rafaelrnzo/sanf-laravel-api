<?php


namespace NbsPhp\Core\Services;


use NbsPhp\Core\Exceptions\UserActivationFailedException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Models\NeedSetupPasswordInterface;

class SendEmailActivationService implements ApplicationServiceInterface
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


    public function execute($dto)
    {
        /** @var AuthModel $user */
        $user = $this->repository->newQuery()->where('username', $dto->email)->first();
        if (!$user) {
            throw new UserActivationFailedException();
        }

        if ($user instanceof NeedSetupPasswordInterface && $user->needActivation()) {
            $user->sendUserActivationNotification();
        }
    }
}
