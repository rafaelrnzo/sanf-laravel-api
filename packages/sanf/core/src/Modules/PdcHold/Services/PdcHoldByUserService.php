<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldGiroRepositoryInterface;
use Sanf\Core\Modules\PdcHold\Repositories\PdcHoldRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

/**
 * @since CR2025
 */
class PdcHoldByUserService
{
    protected PdcHoldRepositoryInterface $pdcHoldRepository;
    protected PdcHoldGiroRepositoryInterface $pdcHoldGiroRepository;
    protected UserRepositoryInterface $userRepository;

    public function __construct(
        PdcHoldRepositoryInterface $pdcHoldRepository,
        PdcHoldGiroRepositoryInterface $pdcHoldGiroRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->pdcHoldRepository = $pdcHoldRepository;
        $this->pdcHoldGiroRepository = $pdcHoldGiroRepository;
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
