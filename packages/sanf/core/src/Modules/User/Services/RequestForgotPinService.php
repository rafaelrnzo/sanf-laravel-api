<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Exceptions\PasswordDoesntMatchException;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class RequestForgotPinService implements ApplicationServiceInterface
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

        $isMatch = Hash::check($dto->password, $user->password);
        if (!$isMatch) {
            throw new PasswordDoesntMatchException();
        }

        // TODO set code length into dynamic variable
        $resetPinCode = random_int(pow(10, 4 - 1), pow(10, 4) - 1);
        $resetPinExpiredAt = Carbon::now()->addDays();
        $this->repository->update([
            'reset_pin_code' => $resetPinCode,
            'reset_pin_expired_at' => $resetPinExpiredAt,
            'updated_at' => Carbon::now(),
        ], $user->id);

        // TODO use transformer
        return (object) [
            'reset_pin_code' => $resetPinCode,
            'reset_pin_expired_at' => $resetPinExpiredAt,
        ];
    }
}
