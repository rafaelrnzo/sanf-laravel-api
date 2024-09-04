<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use NbsPhp\Core\Exceptions\InvalidCredentialException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Dtos\PostDeactivateAccountDto;
use Sanf\Core\Modules\User\Enums\UserAuthLogStatusEnum;
use Sanf\Core\Modules\User\Exceptions\InvalidRequestDeletionAccountException;
use Sanf\Core\Modules\User\Jobs\SendRequestDeletionAccountForAdminNotification;
use Sanf\Core\Modules\User\Jobs\SendRequestDeletionAccountForUserNotification;
use Sanf\Core\Modules\User\Repositories\UserAuthLogRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Specifications\UserAuthLogSpecificationFactoryInterface;

class PostDeactivateAccountService implements ApplicationServiceInterface
{
    protected UserRepositoryInterface $repository;
    public UserAuthLogRepositoryInterface $logRepository;
    private UserAuthLogSpecificationFactoryInterface $logSpecification;

    /**
     * GetProfileService constructor.
     * @param UserRepositoryInterface $repository
     * @param UserAuthLogRepositoryInterface $logRepository
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserAuthLogRepositoryInterface $logRepository,
        UserAuthLogSpecificationFactoryInterface $logSpecification
    ) {
        $this->repository = $repository;
        $this->logRepository = $logRepository;
        $this->logSpecification = $logSpecification;
    }

    public function execute($dto = null)
    {
        if (!$token = Auth::attempt([
            'username' => $dto->username,
            'password' => $dto->password,
        ])) {
            throw new InvalidCredentialException();
        }

        $user = Auth::user();
        if (!$user) {
            throw new UserNotFoundException();
        }

        $totalLog = $this->logRepository->size(
            $this->logSpecification->paginateByUserId($dto->userId, [
                UserAuthLogStatusEnum::SUBMIT,
                UserAuthLogStatusEnum::APPROVE,
            ])
        );

        if ($totalLog > 0) {
            throw new InvalidRequestDeletionAccountException();
        }

        $newLog = $this->logRepository->create([
            'xid' => nano_id(),
            'user_id' => $user->id,
            'email' => $user->username,
            'personal_xid' => $user->personal_xid,
            'status_id' => UserAuthLogStatusEnum::SUBMIT,
            'restore_expired_at' => (string) Carbon::now()->addDays(),
            'created_by' => json_encode([
                'type' => 10,
                'user_id' => $user->id,
                'full_name' => $user->full_name,
            ]),
        ]);

        $dto = new PostDeactivateAccountDto([
            'xid' => $newLog->xid,
            'user_id' => $newLog->user_id,
            'email' => $newLog->email,
            'personal_xid' => $newLog->personal_xid,
            'status_id' => $newLog->status_id,
            'restore_expired_at' => Carbon::parse(optional($newLog)->restore_expired_at),
            'created_by' => json_decode($newLog->created_by),
        ]);

        $composeEmail = [
            'xid' => $dto->xid,
            'name' => $user->full_name,
            'restoreExpiredAt' => Carbon::parse(optional($newLog)->restore_expired_at)
                ->timezone('Asia/Jakarta')
                ->format('d F Y H:i'),
            'createdAt' => Carbon::parse(optional($newLog)->created_at)
                ->timezone('Asia/Jakarta')
                ->format('d F Y H:i'),
        ];

        dispatch(
            new SendRequestDeletionAccountForAdminNotification(
                $composeEmail,
                explode(',', config('sanf-mobile.mail_to_admin'))
            )
        );
        dispatch(
            new SendRequestDeletionAccountForUserNotification($composeEmail, [$newLog->email])
        );

        return $dto;
    }
}
