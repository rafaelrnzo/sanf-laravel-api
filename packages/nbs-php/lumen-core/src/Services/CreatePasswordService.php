<?php

namespace NbsPhp\Core\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class CreatePasswordService implements ApplicationServiceInterface
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

        return $this->repository->update([
            'password' => bcrypt($dto->password),
            'password_updated_at' => Carbon::now(),
        ], $user->id);
    }
}
