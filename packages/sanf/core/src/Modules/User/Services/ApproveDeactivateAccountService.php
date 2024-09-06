<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Enums\UserAuthLogStatusEnum;
use Sanf\Core\Modules\User\Exceptions\InvalidRequestDeletionAccountException;
use Sanf\Core\Modules\User\Exceptions\RequestDeletionAccountNotFoundException;
use Sanf\Core\Modules\User\Jobs\SendApprovalRequestDeletionAccountNotification;
use Sanf\Core\Modules\User\Repositories\UserAuthLogRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Specifications\UserAuthLogSpecificationFactoryInterface;

class ApproveDeactivateAccountService implements ApplicationServiceInterface
{
    public UserRepositoryInterface $repository;
    public UserAuthLogRepositoryInterface $logRepository;
    public UserAuthLogSpecificationFactoryInterface $logSpecification;

    /**
     * GetProfileService constructor.
     * @param UserAuthLogRepositoryInterface $logRepository
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserAuthLogRepositoryInterface $logRepository,
        UserAuthLogSpecificationFactoryInterface $logSpecification
    ) {
        $this->logRepository = $logRepository;
        $this->logSpecification = $logSpecification;
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $userAccountRequest = $this->logRepository->findByXid($dto->xid);
        if (!$userAccountRequest) {
            throw new RequestDeletionAccountNotFoundException();
        }

        if ($userAccountRequest->status_id !== UserAuthLogStatusEnum::SUBMIT) {
            throw new InvalidRequestDeletionAccountException();
        }

        $user = $this->repository->findById($userAccountRequest->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $createdBy = (array) $dto;
        unset($createdBy['xid']);

        $this->logRepository->update([
            'id' => $userAccountRequest->id,
            'status_id' => UserAuthLogStatusEnum::APPROVE,
            'restore_expired_at' => null,
            'created_by' => json_encode(array_merge($createdBy, ['type' => 20])),
        ]);

        $this->repository->update([
            'status_id' => UserStatus::DEACTIVATE,
            'updated_at' => Carbon::now(),
            'deleted_at' => Carbon::now(),
        ], $userAccountRequest->user_id);

        $user = $this->repository->findById($userAccountRequest->user_id);

        if ($user->oauth) {
            $user->oauth->delete();
        }

        dispatch(new SendApprovalRequestDeletionAccountNotification(['name' => $user->full_name], $user->username));

        return $dto;
    }
}
