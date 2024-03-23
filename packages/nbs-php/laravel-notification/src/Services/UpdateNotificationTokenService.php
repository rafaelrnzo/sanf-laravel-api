<?php

namespace NbsPhp\Notification\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Enums\NotificationChannelEnum;
use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;

class UpdateNotificationTokenService implements ApplicationServiceInterface
{
    protected $repository;

    public function __construct(UserNotificationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        if ($dto->notificationChannel !== NotificationChannelEnum::FCM) {
            throw new \Exception('Notification: update token unsupported channel');
        }
        $this->repository->updateFcmToken($dto->userId, $dto->notificationToken);
    }
}
