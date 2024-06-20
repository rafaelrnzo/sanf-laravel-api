<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Carbon\Carbon;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Database\QueryException;
use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;

class SendNotificationPlafondDisbursementSubmittedForClientUseCase implements ApplicationServiceInterface
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

    public function execute($userId = null)
    {
        $data = [
            'xid' => nano_id(),
            'title' => __('Pengajuan Anda Berhasil'),
            'subtitle' => __('Sukses pengajuan pencairan plafond'),
            'body' => __('Pengajuan pencairan plafond Anda telah berhasil dikirim dan sedang dalam proses.'),
            'type' => (string) NotificationTypeEnum::INFO,
            'screen' => '',
            'published_at' => Carbon::now(),
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ];

        // send notification
        // TODO create self service of send notification using event service
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
