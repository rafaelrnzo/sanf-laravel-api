<?php

namespace NbsPhp\Notification\Services;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Dtos\BrowseNotificationByUserRequestDto;
use NbsPhp\Notification\Dtos\BrowseNotificationByUserResponseDto;

final class BrowseNotificationByUserService extends NotificationService implements ApplicationServiceInterface
{
    /**
     * @param BrowseNotificationByUserRequestDto $dto
     * @return BrowseNotificationByUserResponseDto
     */
    public function execute($dto = null)
    {
        $result = $this->notificationRepository->getNotificationsByUserIdAndTypes(
            $dto->userId,
            $dto->keyword,
            null,
            null,
            $dto->skip,
            $dto->limit,
            $dto->timestamp
        );
        $total = $this->notificationRepository->getNotificationsByUserIdAndTypesCount(
            $dto->userId,
            $dto->keyword
        );

        $data = array_map(function ($item) {
            return (object) [
                'xid' => $item->xid,
                'type' => $item->type,
                'userId' => $item->user_id,
                'data' => $item->data,
                'readAt' => CarbonImmutable::make($item->read_at),
                'createdAt' => CarbonImmutable::make($item->created_at),
                'updatedAt' => CarbonImmutable::make($item->updated_at),
            ];
        }, $result);

        return new BrowseNotificationByUserResponseDto([
            'data' => $data,
            'paginate' => [
                'total' => (int) $total,
                'count' => count($data),
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sortBy' => $dto->sortBy,
            ],
        ]);
    }
}
