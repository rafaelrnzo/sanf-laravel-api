<?php

namespace Sanf\Core\Modules\PdcHold\Services;

use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Database\QueryException;
use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;

/**
 * @since CR2025
 */
class SendNotificationPdcHoldSubmissionUpdateForClientService implements ApplicationServiceInterface
{
    private $userNotificationRepository;
    private $pushNotificationService;

    public function __construct(
        UserNotificationRepositoryInterface $userNotificationRepository,
        PushNotificationServiceInterface $pushNotificationService
    ) {
        $this->userNotificationRepository = $userNotificationRepository;
        $this->pushNotificationService = $pushNotificationService;
    }

    public function execute($dto = null)
    {
        $userId = $dto['userId'] ?? null;
        $data = $dto['payload'];

        if (is_null($userId)) {
            return;
        }

        $fcmTokens = $this->userNotificationRepository->getFcmTokens($userId);

        try {
            $this->userNotificationRepository->create([
                'xid' => $data['xid'],
                'type' => (int) $data['type'],
                'user_id' => $userId,
                'data' => $data,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() == '23505') {
                throw new NotificationInvalidException('ID not unique');
            }
            throw $exception;
        }
        foreach (array_unique($fcmTokens) as $fcmToken) {
            try {
                $this->pushNotificationService->sendToDevice($fcmToken, $data);
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
