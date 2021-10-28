<?php


namespace Sanf\Core\Modules\Financing\Services;


use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Financing\Repositories\FinancingRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class FinancingByUserService extends FinancingService
{
    protected AuthModel $userRepository;

    public function __construct(FinancingRepositoryInterface $repository, AuthModel $userRepository)
    {
        parent::__construct($repository);
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
