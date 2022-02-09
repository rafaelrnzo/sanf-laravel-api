<?php


namespace NbsPhp\Notification\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Repositories\UserRepositoryInterface;
use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;

class NotificationService
{
    protected $notificationRepository;
    protected $userRepository;

    public function __construct(
        UserNotificationRepositoryInterface $notificationRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
    }

    public function notificationCount($userId, $metadataKeys = ['unread_count'])
    {
        $unreadCount = $this->userRepository->getMetadata($userId, $metadataKeys);
        return json_decode(json_encode([
            'unread_count' => optional($unreadCount)->value,
        ]));
    }

    public function getUnreadUserNotifications($userId, $input)
    {
        $groupKey = $input['group'] ?? null;
        //TODO LOAD CONFIG SOMEWHERE
        $groups = config('notifications.groups') ?? [];
        $types = $groups[$groupKey] ?? null;
        $lastId = $input['last_id'] ?? null;
        $limit = $input['limit'] ?? 20;
        return $this->notificationRepository->getNotificationsByUserIdAndTypes($userId, $lastId, $limit, $types, false);
    }

    /**
     * @param $userId
     * @param $input
     * @throws \Exception
     */
    public function markNotificationAsRead($userId, $input)
    {
        $notificationIds = $input["ids"] ?? [];
        //TODO LOAD SOMEWHERE
        $metadata = [];
        foreach (config('notifications.types') as $type) {
            if (isset($type[0])) { // check if consist of array data
                foreach ($type as $typeItem) {
                    $eventType = $typeItem['data']['type'] ?? null;
                    $typeMetadata = $typeItem['metadata'] ?? [];
                    foreach ($typeMetadata as $key => $value) {
                        $metadata[$value][] = $eventType;
                    }
                }
            } else {
                $eventType = $type['data']['type'] ?? null;
                $typeMetadata = $type['metadata'] ?? [];
                foreach ($typeMetadata as $key => $value) {
                    $metadata[$value][] = $eventType;
                }
            }
        }
        //make sure event not duplicate
        foreach ($metadata as $key => $value) {
            $metadata[$key] = array_unique($metadata[$key]);
        }
        foreach ($metadata as $metadataKey => $notificationTypes) {
            $readCount = $this->notificationRepository->setUserNotificationReadByIdsAndTypes($userId, $notificationIds, $notificationTypes, Carbon::now());
            if ($readCount > 0) {
                $this->optimisticUpdateMetadataUnreadCount($userId, $metadataKey, $notificationTypes);
            }
        }
    }

    /**
     * @param $userId
     * @param $notificationTypes
     * @param $metadataKey
     * @param int $tryCount
     * @param int $maxTry
     * @throws \Exception
     */
    protected function optimisticUpdateMetadataUnreadCount($userId, $metadataKey, $notificationTypes = null,  $tryCount = 0, $maxTry = 10)
    {
        $tryCount++;
        if ($tryCount >= $maxTry) {
            throw new \Exception("Notification: failed to update metadata, optimistic locking max try reached");
        }
        if(is_null($notificationTypes)){
            $unreadCount = $this->notificationRepository->getUserNotificationUnreadCount($userId);
        }else{
            $unreadCount = $this->notificationRepository->getUserNotificationUnreadCountByTypes($userId, $notificationTypes);
        }
        $notificationMetadata = $this->userRepository->getMetadata($userId, $metadataKey);
        if (is_null($notificationMetadata)) {
            return $this->userRepository->createMetadata($userId, $metadataKey, $unreadCount);
        }
        $success = $this->userRepository->updateMetadata($userId, $metadataKey, $unreadCount, $notificationMetadata->version);
        if (!$success) {
            return $this->optimisticUpdateMetadataUnreadCount($userId, $metadataKey, $notificationTypes, $tryCount);
        }
    }

    public function insertUserNotifications($userId, $input)
    {
        DB::transaction(function () use ($userId, $input) {
            $idExt = nano_id();

            $this->notificationRepository->create([
                'xid' => $idExt,
                'type' => $input['type'],
                'notifiable_type' => 'user',
                'notifiable_id' => $userId,
                'data' => array_merge($input, [
                    'xid' => $idExt,
                    'body_formatted' => $input['body'],
                    'click_action' => $input['click_action'] ?? '',
                    'link' => '',
                    'icon' => file_get_url($input['icon'] ?? config('notifications.default_icon')),
                ]),
            ]);
        });
    }
}
