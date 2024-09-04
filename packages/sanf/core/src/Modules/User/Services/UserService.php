<?php

namespace Sanf\Core\Modules\User\Services;

use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class UserService
{
    protected $userRepository;

    /**
     * UserService constructor.
     * @param $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
