<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Database\QueryException;
use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;
use Sanf\Dashboard\Modules\Notification\Repositories\FcmNotificationEloquentRepository;
use Sanf\Dashboard\Modules\Notification\Repositories\NotificationEloquentRepository;
use Sanf\Dashboard\Modules\User\Repositories\UserEloquentRepository;

class SendNotificationPlafondDisbursementForCustomerUseCase implements ApplicationServiceInterface
{
    private $userDashboardRepository;
    private $notificationDashboardRepository;
    private $fcmNotificationRepository;
    private $pushNotificationService;

    public function __construct(
        UserEloquentRepository $userDashboardRepository,
        NotificationEloquentRepository $notificationEloquentRepository,
        FcmNotificationEloquentRepository $fcmNotificationEloquentRepository,
        PushNotificationServiceInterface $pushNotificationService
    ) {
        $this->userDashboardRepository = $userDashboardRepository;
        $this->notificationDashboardRepository = $notificationEloquentRepository;
        $this->fcmNotificationRepository = $fcmNotificationEloquentRepository;
        $this->pushNotificationService = $pushNotificationService;
    }

    public function execute($dto = null)
    {
        $notificationData = $dto['notificationData'];
        $payloadNotification = $dto['payloadNotification'];

        try {
            $this->notificationDashboardRepository->create($notificationData);
        } catch (QueryException $exception) {
            if ($exception->getCode() == '23505') {
                throw new NotificationInvalidException('ID not unique');
            }
            throw $exception;
        }

        $user = $this->userDashboardRepository->findUserAuthByBowheerId($dto['bowheerId']);
        $tokens = array_map(function ($fcmSession) {
            return $fcmSession->token;
        }, $user->fcm_tokens ?? []);

        foreach (array_unique($tokens) as $token) {
            try {
                $this->pushNotificationService->sendToDevice($token, $payloadNotification);
            } catch (InvalidToken $exception) {
                $this->fcmNotificationRepository->deleteByToken($token);
                report($exception);
            } catch (MessagingException $exception) {
                $this->fcmNotificationRepository->deleteByToken($token);
                report($exception);
            }
        }
    }
}
