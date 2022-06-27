<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Exceptions\PinResetExpiredException;
use Sanf\Core\Modules\User\Exceptions\PinResetInvalidException;
use Sanf\Core\Modules\User\Exceptions\PinResetCodeNotMatchException;

class ResetPinService implements ApplicationServiceInterface
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

        if (!isset($user->reset_pin_code)) {
            throw new PinResetInvalidException();
        }

        if ($user->reset_pin_expired_at < Carbon::now()) {
            throw new PinResetExpiredException();
        }

        $isMatch = ($dto->code == $user->reset_pin_code);
        if (!$isMatch) {
            throw new PinResetCodeNotMatchException();
        }

        $user->update([
            'pin' => bcrypt($dto->pin),
            'pin_updated_at' => Carbon::now(),
            'reset_pin_code' => null,
            'reset_pin_expired_at' => null,
            'updated_at' => Carbon::now(),
        ]);

        return true;
    }
}
