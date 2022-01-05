<?php


namespace NbsPhp\Notification\Repositories;


interface UserNotificationRepositoryInterface
{
    public function create($data);

    public function updateFcmToken($userId, $token);

    public function getFcmTokens($userId);

    public function getNotificationsByUserIdAndTypes($userId, $keyword = null, $types = null, $isRead = null, $sortBy = null, $skip = null, $limit = null, $timestamp = null);

    public function getNotificationsByUserIdAndTypesCount($userId, $keyword = null, $types = null, $isRead = null);

    public function getUserNotificationUnreadCountByTypes($userId, $notificationTypes);

    public function setUserNotificationReadByIdsAndTypes($userId, $notificationIds, $notificationTypes, $readAt);
}
