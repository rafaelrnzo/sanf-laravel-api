<?php


namespace NbsPhp\Notification\Repositories;


use Carbon\Carbon;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use NbsPhp\Notification\Models\NotificationChannelModel;
use NbsPhp\Notification\Models\UserNotificationModel;
use NbsPhp\Notification\Models\UserSessionModel;

class EloquentUserNotificationRepository extends AbstractEloquentRepository implements UserNotificationRepositoryInterface
{
    protected UserNotificationModel $notificationModel;
    protected UserSessionModel $userSessionModel;

    public function __construct(UserNotificationModel $notificationModel, UserSessionModel $userSessionModel)
    {
        $this->notificationModel = $notificationModel;
        $this->userSessionModel = $userSessionModel;
    }

    public function create($data)
    {
        $this->notificationModel->newQuery()->forceCreate($data);
    }

    public function getNotificationsByUserIdAndTypes(
        $userId,
        $keyword = null,
        $types = null,
        $isRead = null,
        $sortBy = null,
        $skip = null,
        $limit = null,
        $timestamp = null
    ) {
        switch ($sortBy) {
            case 'earliest':
            case 'oldest':
                $orderBy = 'created_at';
                $orderDirection = 'ASC';
                break;
            case 'latest':
            case 'newest':
            default:
                $orderBy = 'created_at';
                $orderDirection = 'DESC';
        }
        $notifications = $this->notificationModel->newQuery()
            ->where('user_id', $userId)
            ->when($types, function ($query) use ($types) {
                $query->whereIn('type', $types);
            })
            ->when(isset($timestamp), function ($query) use ($timestamp) {
                $query->where('created_at', '<', $timestamp);
            })
            ->when(isset($isRead), function ($query) use ($isRead) {
                if ($isRead) {
                    $query->whereNotNull('read_at');
                } else {
                    $query->whereNull('read_at');
                }
            })
            ->orderBy($orderBy, $orderDirection)
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('data->title', 'ILIKE', '%' . $keyword . '%');
            })->when($skip, function ($query) use ($skip) {
                return $query->skip($skip);
            })->when($limit, function ($query) use ($limit) {
                return $query->limit($limit);
            })->when($timestamp, function ($query) use ($timestamp) {
                return $query->where('created_at', '>', Carbon::createFromTimestamp($timestamp));
            })
            ->get();

        return $this->stripEloquentModel($notifications);
    }

    public function getNotificationsByUserIdAndTypesCount($userId, $keyword = null, $types = null, $isRead = null)
    {
        return $this->notificationModel->newQuery()
            ->where('user_id', $userId)
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('data->title', 'ILIKE', '%' . $keyword . '%');
            })
            ->when($types, function ($query) use ($types) {
                $query->whereIn('type', $types);
            })
            ->when(isset($isRead), function ($query) use ($isRead) {
                if ($isRead) {
                    $query->whereNotNull('read_at');
                } else {
                    $query->whereNull('read_at');
                }
            })
            ->count();
    }


    public function updateFcmToken($userId, $token)
    {
        $this->userSessionModel->newQuery()
            ->where('notification_token', $token)
            ->delete();
        $tokens = $this->userSessionModel->newQuery()
            ->where('user_id', $userId)
            ->update([
                'notification_channel_id' => NotificationChannelModel::FCM,
                'notification_token' => $token,
            ]);
        return json_decode(json_encode($tokens));
    }

    public function setUserNotificationReadByIdsAndTypes($userId, $notificationIds, $notificationType, $readAt)
    {
        return $this->notificationModel->newQuery()
            ->whereNull('read_at')
            ->where('user_id', $userId)
            ->whereIn('type', $notificationType)
            ->when(!empty($notificationIds), function ($query) use ($notificationIds) {
                $query->whereIn('id', $notificationIds);
            })
            ->update(["read_at" => $readAt]);
    }

    public function getUserNotificationUnreadCountByTypes($userId, $notificationType)
    {
        return $this->notificationModel->newQuery()
            ->whereNull('read_at')
            ->where('user_id', $userId)
            ->whereIn('type', $notificationType)
            ->count();
    }

    public function getFcmTokens($userId)
    {
        $tokens = $this->userSessionModel->newQuery()
            ->select('notification_token')
            ->where('notification_channel_id', NotificationChannelModel::FCM)
            ->where('user_id', $userId)
            ->get();
        return json_decode(json_encode($tokens));
    }
}
