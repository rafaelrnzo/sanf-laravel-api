<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Carbon\Carbon;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Database\QueryException;
use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Dashboard\Modules\Notification\Repositories\NotificationEloquentRepository;
use Sanf\Dashboard\Modules\User\Repositories\UserEloquentRepository;

class SendNotificationPlafondDisbursementSubmittedForCustomerUseCase implements ApplicationServiceInterface
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
        $webPartnerUrl = config('web-partner.base_url') . "/plafond/disbursements/{$dto->disbursementXid}/submissions/{$dto->submissionXid}";
        $body = "<span><b>{$dto->client}</b> telah melakukan pengajuan dan membutuhkan review Anda. Periksa sekarang!</span>";
        $notificationData = [
            'xid' => nano_id(),
            'notifiable_type' => 'bowheer_id',
            'notifiable_id' => $dto->bowheerId,
            'body' => $body,
            'url' => "{$webPartnerUrl}",
        ];

        try {
            $this->notificationDashboardRepository->create($notificationData);
        } catch (QueryException $exception) {
            if ($exception->getCode() == '23505') {
                throw new NotificationInvalidException('ID not unique');
            }
            throw $exception;
        }

        $user = $this->userDashboardRepository->findUserAuthByBowheerId($dto->bowheerId);

        $payloadNotification = [
            'xid' => nano_id(),
            'title' => __('Pengajuan Pencairan Plafond'),
            'subtitle' => __('Pengajuan Pencairan plafond'),
            'body' => strip_tags($body),
            'type' => (string) NotificationTypeEnum::INFO,
            'screen' => '',
            'published_at' => Carbon::now(),
            'click_action' => "url:{$webPartnerUrl}",
        ];

        try {
            $this->pushNotificationService->sendToDevice($user->fcmToken, $payloadNotification);
        } catch (InvalidToken $exception) {
            report($exception);
        } catch (MessagingException $exception) {
            report($exception);
        }
    }
}
