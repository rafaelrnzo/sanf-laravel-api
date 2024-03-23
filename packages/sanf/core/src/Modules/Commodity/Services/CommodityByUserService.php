<?php

namespace Sanf\Core\Modules\Commodity\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Commodity\Repositories\CommodityRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class CommodityByUserService extends CommodityService
{
    protected AuthModel $userRepository;

    public function __construct(CommodityRepositoryInterface $commodityRepository, AuthModel $userRepository)
    {
        parent::__construct($commodityRepository);
        $this->userRepository = $userRepository;
    }

    protected function findUserOrFail($userId)
    {
        $user = $this->userRepository->newQuery()->find($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
