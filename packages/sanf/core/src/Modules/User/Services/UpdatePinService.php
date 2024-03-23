<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Exceptions\PinDoesntMatchException;
use Sanf\Core\Modules\User\Exceptions\PinNewCodeReusedException;

class UpdatePinService implements ApplicationServiceInterface
{
    protected $repository;

    /**
     * GetProfileService constructor.
     * @param $repository
     */
    public function __construct(AuthModel $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $user = $this->repository->newQuery()->find($dto->userId);
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

        $user->update([
            'pin' => bcrypt($dto->new_pin),
            'pin_updated_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return true;
    }
}
