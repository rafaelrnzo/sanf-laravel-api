<?php

namespace NbsPhp\Core\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use NbsPhp\Core\Exceptions\InvalidCredentialException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class ChangePasswordService implements ApplicationServiceInterface
{
    protected $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null): bool
    {
        $user = $this->repository->findById($dto->userId);
        if(!$user){
            throw new UserNotFoundException();
        }
        if(!Hash::check($dto->currentPassword, $user->password)){
            throw new InvalidCredentialException();
        }

        return $this->repository->update([
            'password' => bcrypt($dto->newPassword),
            'password_updated_at' => Carbon::now(),
        ], $user->id);
    }
}
