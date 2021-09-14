<?php


namespace NbsPhp\Core\Services;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use NbsPhp\Core\Exceptions\VerifyEmailFailedException;
use NbsPhp\Core\Models\AuthModel;

class SendEmailVerificationService implements ApplicationServiceInterface
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


    public function execute($dto = null)
    {
        /** @var AuthModel $user */
        $user = $this->repository->newQuery()->where('username', $dto->email)->first();
        if (!$user) {
            throw new VerifyEmailFailedException();
        }

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
    }
}
