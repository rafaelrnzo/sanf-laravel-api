<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Database\QueryException;
use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;
use Sanf\Dashboard\Modules\Notification\Repositories\NotificationEloquentRepository;
use Sanf\Dashboard\Modules\User\Repositories\UserEloquentRepository;

class SendNotificationPlafondDisbursementForCustomerUseCase implements ApplicationServiceInterface
{
    private $userDashboardRepository;
    private $notificationDashboardRepository;
    private $pushNotificationService;

    public function __construct(
        UserEloquentRepository $userDashboardRepository,
        NotificationEloquentRepository $notificationEloquentRepository,
        PushNotificationServiceInterface $pushNotificationService
    ) {
        $this->userDashboardRepository = $userDashboardRepository;
        $this->notificationDashboardRepository = $notificationEloquentRepository;
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

        try {
            $this->pushNotificationService->sendToDevice($user->fcmToken, $payloadNotification);
        } catch (InvalidToken $exception) {
            report($exception);
        } catch (MessagingException $exception) {
            report($exception);
        }
    }
}
