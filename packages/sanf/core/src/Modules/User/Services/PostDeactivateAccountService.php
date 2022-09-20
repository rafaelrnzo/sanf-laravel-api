<?php

namespace Sanf\Core\Modules\User\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Dtos\PostDeactivateAccountDto;
use Sanf\Core\Modules\User\Enums\UserAuthLogStatusEnum;
use Sanf\Core\Modules\User\Exceptions\InvalidRequestDeletionAccountException;
use Sanf\Core\Modules\User\Jobs\SendRequestDeletionAccountJob;
use Sanf\Core\Modules\User\UserAuthLogModel;

class PostDeactivateAccountService implements ApplicationServiceInterface
{
    protected AuthModel $repository;
    public UserAuthLogModel $logRepository;

    /**
     * GetProfileService constructor.
     * @param AuthModel $repository
     * @param UserAuthLogModel $logRepository
     */
    public function __construct(AuthModel $repository, UserAuthLogModel $logRepository)
    {
        $this->repository = $repository;
        $this->logRepository = $logRepository;
    }

    public function execute($dto = null)
    {
        $user = $this->repository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $totalLog = $this->logRepository->newQuery()
            ->where('user_id', '=', $dto->userId)
            ->where('status_id', '!=', UserAuthLogStatusEnum::REJECT)
            ->count();

        if ($totalLog > 0) {
            throw new InvalidRequestDeletionAccountException();
        }

        $newLog = $this->logRepository->newQuery()
            ->forceCreate([
                'xid' => nano_id(),
                'user_id' => $user->id,
                'email' => $user->username,
                'personal_xid' => $user->personal_xid,
                'status_id' => UserAuthLogStatusEnum::SUBMIT,
                'restore_expired_at' => Carbon::now()->addDays(),
                'created_by' => json_encode([
                    'type' => 10,
                    'user_id' => $user->id,
                    'full_name' => $user->full_name,
                ]),
            ]);

        $recipients = explode(',', config('sanf-mobile.mail_to_admin'));
        dispatch(new SendRequestDeletionAccountJob([
            'name' => $user->full_name,
            'restoreExpiredAt' => $newLog->restore_expired_at
                ->timezone('Asia/Jakarta')
                ->format('d m Y H:i'),
            'createdAt' => $newLog->created_at
                ->timezone('Asia/Jakarta')
                ->format('d m Y H:i'),
        ], $recipients));

        return new PostDeactivateAccountDto([
            'xid' => $newLog->xid,
            'user_id' => $newLog->user_id,
            'email' => $newLog->email,
            'personal_xid' => $newLog->personal_xid,
            'status_id' => $newLog->status_id,
            'restore_expired_at' => $newLog->restore_expired_at,
            'created_by' => json_decode($newLog->created_by),
        ]);
    }
}
