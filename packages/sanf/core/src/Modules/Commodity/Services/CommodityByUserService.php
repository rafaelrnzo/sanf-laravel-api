<?php

namespace Sanf\Core\Modules\Commodity\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Commodity\Repositories\CommodityRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class CommodityByUserService extends CommodityService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(CommodityRepositoryInterface $commodityRepository, UserRepositoryInterface $userRepository)
    {
        parent::__construct($commodityRepository);
        $this->userRepository = $userRepository;
    }

    protected function findUserOrFail($userId)
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
