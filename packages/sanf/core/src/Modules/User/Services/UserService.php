<?php


namespace Sanf\Core\Modules\User\Services;


use Sanf\Core\Modules\User\AuthModel;

class UserService
{
    protected $userRepository;

    /**
     * UserService constructor.
     * @param $userRepository
     */
    public function __construct(AuthModel $userRepository)
    {
        $this->userRepository = $userRepository;
    }

}
