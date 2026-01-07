<?php

namespace Sanf\Core\Modules\Notification\Services;

use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Database\QueryException;
use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserRequestDto;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;
use Sanf\Dashboard\Modules\Notification\Repositories\NotificationEloquentRepository;
use Sanf\Dashboard\Modules\Notification\Repositories\SanfindUserFcmNotificationEloquentRepository;

class AddPushNotificationUserService
{
    private $userNotificationRepository;
    private $pushNotificationService;
    private $sanfindUserFcmNotificationRepository;
    private $notificationDashboardRepository;

    public function __construct(
        UserNotificationRepositoryInterface $userNotificationRepository,
        PushNotificationServiceInterface $pushNotificationService,
        SanfindUserFcmNotificationEloquentRepository $sanfindUserFcmNotificationRepository,
        NotificationEloquentRepository $notificationEloquentRepository
    ) {
        $this->userNotificationRepository = $userNotificationRepository;
        $this->pushNotificationService = $pushNotificationService;
        $this->sanfindUserFcmNotificationRepository = $sanfindUserFcmNotificationRepository;
        $this->notificationDashboardRepository = $notificationEloquentRepository;
    }

    public function execute(AddPushNotificationUserRequestDto $dto)
    {
        $userId = $dto->user_id;
        $payload = $dto->payload;

        $fcmTokens = $this->userNotificationRepository->getFcmTokens($userId);

        if (!empty($dto->dashboardNotification)) {
            $customerWebFcmTokens = $this->sanfindUserFcmNotificationRepository->findActive([
                'sanfind_userid' => $userId,
            ]);

            foreach ($customerWebFcmTokens as $customerWebFcmToken) {
                if (!empty($customerWebFcmToken->token)) {
                    $fcmTokens[] = $customerWebFcmToken->token;
                }
            }
        }

        try {
            $this->userNotificationRepository->create([
                'xid' => $payload->xid,
                'type' => $payload->type,
                'user_id' => $userId,
                'data' => $payload->toArray(),
            ]);

            if (!empty($dto->dashboardNotification)) {
                $this->notificationDashboardRepository->create($dto->dashboardNotification->toArray());
            }
        } catch (QueryException $exception) {
            if ($exception->getCode() == '23505') {
                throw new NotificationInvalidException('ID not unique');
            }
            throw $exception;
        }
        foreach (array_unique($fcmTokens) as $fcmToken) {
            try {
                $this->pushNotificationService->sendToDevice($fcmToken, $payload->toArray());
            } catch (InvalidToken $exception) {
                $this->userNotificationRepository->deleteFcmToken($fcmToken);
                report($exception);
            } catch (MessagingException $exception) {
                $this->userNotificationRepository->deleteFcmToken($fcmToken);
                report($exception);
            }
        }
    }
}
