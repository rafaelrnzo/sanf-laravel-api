<?php

namespace Sanf\Dashboard\Modules\Notification\Repositories;

use Illuminate\Support\Carbon;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Dashboard\Modules\User\Models\SanfindUserFcmNotificationTokenModel;

/**
 * @since CR2025
 */
class SanfindUserFcmNotificationEloquentRepository extends AbstractEloquentRepository
{
    protected SanfindUserFcmNotificationTokenModel $fcmSessionModel;

    public function __construct(SanfindUserFcmNotificationTokenModel $fcmSessionModel)
    {
        $this->fcmSessionModel = $fcmSessionModel;
    }

    public function findActive(array $filters, array $columns = ['*'])
    {
        $now = Carbon::now();

        return $this->fcmSessionModel->newQuery()
            ->select($columns)
            ->where($filters)
            ->where('expiresAt', '>', $now)
            ->orderByDesc($this->fcmSessionModel->getCreatedAtColumn())
            ->get();
    }
}
