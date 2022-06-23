<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Exceptions\ExpiredResetPinCodeException;
use Sanf\Core\Modules\User\Exceptions\InvalidResetPinException;
use Sanf\Core\Modules\User\Exceptions\ResetPinCodeDoesntMatchException;

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
            throw new InvalidResetPinException();
        }

        if ($user->exp_reset_pin_at < Carbon::now()) {
            throw new ExpiredResetPinCodeException();
        }

        $isMatch = ((int)$dto->code === (int) $user->reset_pin_code);
        if (!$isMatch) {
            throw new ResetPinCodeDoesntMatchException();
        }

        $user->update([
            'pin' => bcrypt($dto->pin),
            'pin_updated_at' => Carbon::now(),
            'reset_pin_code' => null,
            'exp_reset_pin_at' => null,
            'updated_at' => Carbon::now(),
        ]);

        return true;
    }
}
