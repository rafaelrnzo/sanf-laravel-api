<?php

namespace Sanf\Core\Modules\Notification\Services;

use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;

class NotificationByUserService
{
    protected UserNotificationRepositoryInterface $repository;

    public function __construct(UserNotificationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}
