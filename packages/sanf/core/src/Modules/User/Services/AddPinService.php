<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Exceptions\PinHasCreatedException;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class AddPinService implements ApplicationServiceInterface
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

        if (isset($user->pin_updated_at)) {
            throw new PinHasCreatedException();
        }

        $this->repository->update([
            'pin' => bcrypt($dto->pin),
            'pin_updated_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ], $user->id);

        return true;
    }
}
