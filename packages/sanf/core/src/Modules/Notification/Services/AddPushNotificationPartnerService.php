<?php

namespace Sanf\Core\Modules\Notification\Services;

use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Database\QueryException;
use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationPartnerRequestDto;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;
use Sanf\Dashboard\Modules\Notification\Repositories\FcmNotificationEloquentRepository;
use Sanf\Dashboard\Modules\Notification\Repositories\NotificationEloquentRepository;
use Sanf\Dashboard\Modules\User\Repositories\UserEncryptedEloquentRepository;

class AddPushNotificationPartnerService
{
    private $userDashboardRepository;
    private $notificationDashboardRepository;
    private $fcmNotificationRepository;
    private $pushNotificationService;

    public function __construct(
        UserEncryptedEloquentRepository $userDashboardRepository,
        NotificationEloquentRepository $notificationEloquentRepository,
        FcmNotificationEloquentRepository $fcmNotificationEloquentRepository,
        PushNotificationServiceInterface $pushNotificationService
    ) {
        $this->userDashboardRepository = $userDashboardRepository;
        $this->notificationDashboardRepository = $notificationEloquentRepository;
        $this->fcmNotificationRepository = $fcmNotificationEloquentRepository;
        $this->pushNotificationService = $pushNotificationService;
    }

    public function execute(AddPushNotificationPartnerRequestDto $dto)
    {
        try {
            $this->notificationDashboardRepository->create($dto->dashboardNotification->toArray());
        } catch (QueryException $exception) {
            if ($exception->getCode() == '23505') {
                throw new NotificationInvalidException('ID not unique');
            }
            throw $exception;
        }

        $user = $this->userDashboardRepository->findUserAuthByBowheerId($dto->bowheerId);
        $tokens = array_map(function ($fcmSession) {
            return $fcmSession->token;
        }, $user->fcmTokens ?? []);

        foreach (array_unique($tokens) as $token) {
            try {
                $this->pushNotificationService->sendToDevice($token, $dto->payload->toArray());
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
