<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Enums\UserAuthLogStatusEnum;
use Sanf\Core\Modules\User\Exceptions\InvalidRequestDeletionAccountException;
use Sanf\Core\Modules\User\Exceptions\RequestDeletionAccountNotFoundException;
use Sanf\Core\Modules\User\Jobs\SendApprovalRequestDeletionAccountNotification;
use Sanf\Core\Modules\User\Repositories\UserAuthLogRepositoryInterface;
use Sanf\Core\Modules\User\Specifications\UserAuthLogSpecificationFactoryInterface;

class ApproveDeactivateAccountService implements ApplicationServiceInterface
{
    public AuthModel $repository;
    public UserAuthLogRepositoryInterface $logRepository;
    public UserAuthLogSpecificationFactoryInterface $logSpecification;

    /**
     * GetProfileService constructor.
     * @param UserAuthLogRepositoryInterface $logRepository
     */
    public function __construct(
        AuthModel $repository,
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

        $user = $this->repository->newQuery()->find($userAccountRequest->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $createdBy = (array)$dto;
        unset($createdBy['xid']);

        $this->logRepository->update([
            'id' => $userAccountRequest->id,
            'status_id' => UserAuthLogStatusEnum::APPROVE,
            'restore_expired_at' => null,
            'created_by' => json_encode(array_merge($createdBy, ['type' => 20])),
        ]);

        dispatch(new SendApprovalRequestDeletionAccountNotification(['name' => $user->full_name,], $user->username));

        return $dto;
    }
}
