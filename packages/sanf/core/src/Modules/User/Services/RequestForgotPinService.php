<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Exceptions\PasswordDoesntMatchException;

class RequestForgotPinService implements ApplicationServiceInterface
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

        $isMatch = Hash::check($dto->password, $user->password);
        if (!$isMatch) {
            throw new PasswordDoesntMatchException();
        }

        $user->update([
            'reset_pin_code' => rand(pow(10, 4 - 1), pow(10, 4) - 1), //TODO set code length into dynamic variable
            'reset_pin_expired_at' => Carbon::now()->addDays(),
            'updated_at' => Carbon::now(),
        ]);

        return (object)[
            'reset_pin_code' => $user->reset_pin_code,
            'reset_pin_expired_at' => $user->reset_pin_expired_at,
        ]; // TODO use transformer
    }
}
