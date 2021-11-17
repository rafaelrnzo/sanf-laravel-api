<?php


namespace NbsPhp\Core\Services;


use NbsPhp\Core\Exceptions\UnauthorizedException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Models\NeedSetupPasswordInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VerifyEmailService implements VerifyEmailServiceInterface
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
        $user = $this->repository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new NotFoundHttpException();
        }

        if ($user instanceof NeedSetupPasswordInterface && $user->needActivation()) {
            $user->markUserActivated();
        }

        if (!hash_equals((string)$dto->token, sha1($user->getEmailForVerification()))) {
            throw new UnauthorizedException();
        }

        if (is_null($user->email_verified_at)) {
            $user->markEmailAsVerified();
        }

        return $user;
    }
}
