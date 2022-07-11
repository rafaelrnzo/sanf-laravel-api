<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\UserRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;

final class ESignUserRegisteredService implements ApplicationServiceInterface
{
    protected ESignRepositoryInterface $eSignRepository;

    public function __construct(ESignRepositoryInterface $eSignRepository)
    {
        $this->eSignRepository = $eSignRepository;
    }

    /**
     * @param null $dto
     * @return object
     */
    public function execute($dto = null): object
    {
        // get user base on email
        $user = $this->eSignRepository->findUserByEmail($dto->email);
        if (!$user) {
            throw new UserNotFoundException();
        }

        // update status into complete state
        $user = $this->eSignRepository->updateUser($user->id, [
            'status_id' => UserRegistrationStatusEnum::COMPLETE,
            'updated_at' => Carbon::now(),
        ]);

        // sent complete notification

        return $user;
    }
}
