<?php

namespace Sanf\Core\Modules\User\Services;

use Illuminate\Support\Facades\Hash;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Exceptions\PinDoesntMatchException;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class CheckPinService implements ApplicationServiceInterface
{
    protected $repository;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $user = $this->repository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $isMatch = Hash::check($dto->pin, $user->pin);
        if (!$isMatch) {
            throw new PinDoesntMatchException();
        }

        return true;
    }
}
