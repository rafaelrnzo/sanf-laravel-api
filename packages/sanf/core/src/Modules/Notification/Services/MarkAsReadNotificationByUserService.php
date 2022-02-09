<?php

namespace Sanf\Core\Modules\Notification\Services;



use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Dtos\ReadNotificationByUserRequestDto;
use NbsPhp\Notification\Dtos\ReadNotificationByUserResponseDto;
use NbsPhp\Notification\Services\NotificationService;

final class MarkAsReadNotificationByUserService extends NotificationService implements ApplicationServiceInterface
{
    /**
     * @param ReadNotificationByUserRequestDto $dto
     * @return ReadNotificationByUserResponseDto
     */
    public function execute($dto = null)
    {
        $readCount = $this->notificationRepository->setUserNotificationReadByXids($dto->userId, $dto->xids, Carbon::now());
        return new ReadNotificationByUserResponseDto(['readCount' => $readCount]);
    }
}
