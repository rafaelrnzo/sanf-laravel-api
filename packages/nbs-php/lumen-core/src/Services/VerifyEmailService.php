<?php

namespace NbsPhp\Core\Services;

use NbsPhp\Core\Exceptions\UnauthorizedException;
use NbsPhp\Core\Models\NeedSetupPasswordInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VerifyEmailService implements VerifyEmailServiceInterface
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
            throw new NotFoundHttpException();
        }

        if (!hash_equals((string) $dto->token, hash('sha256', $user->getEmailForVerification()))) {
            throw new UnauthorizedException();
        }

        if ($user instanceof NeedSetupPasswordInterface && $user->needActivation()) {
            $user->markUserActivated();
        }

        if (is_null($user->email_verified_at)) {
            $user->markEmailAsVerified();
        }

        return $user;
    }
}
