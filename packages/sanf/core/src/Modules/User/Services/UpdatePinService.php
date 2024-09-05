<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Exceptions\PinDoesntMatchException;
use Sanf\Core\Modules\User\Exceptions\PinNewCodeReusedException;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class UpdatePinService implements ApplicationServiceInterface
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

        $isMatch = Hash::check($dto->current_pin, $user->pin);
        if (!$isMatch) {
            throw new PinDoesntMatchException();
        }

        $isSame = Hash::check($dto->new_pin, $user->pin);
        if ($isSame) {
            throw new PinNewCodeReusedException();
        }

        $this->repository->update([
            'pin' => bcrypt($dto->new_pin),
            'pin_updated_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ], $user->id);

        return true;
    }
}
