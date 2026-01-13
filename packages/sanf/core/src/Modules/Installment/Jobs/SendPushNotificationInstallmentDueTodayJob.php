<?php

namespace Sanf\Core\Modules\Installment\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Sanf\Core\Modules\Installment\Payloads\SendPushNotificationInstallmentJobPayload;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserDashboardRequestDto;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserPayloadRequestDto;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserRequestDto;
use Sanf\Core\Modules\Notification\Enums\PushNotificationUserNotifiableTypeEnum;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Notification\Services\AddPushNotificationUserService;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

class SendPushNotificationInstallmentDueTodayJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    protected SendPushNotificationInstallmentJobPayload $payload;

    public function __construct(SendPushNotificationInstallmentJobPayload $payload)
    {
        $this->payload = $payload;
    }

    public function handle(
        AddPushNotificationUserService $service,
        UserRepositoryInterface $userRepository
    ) {
        $totalAmount = $this->payload->total_amount;
        $userProfileXid = $this->payload->customer_id_sanfind;
        $totalAmountCurrency = format_currency($totalAmount);
        $contractNo = $this->payload->contract_no;
        $dueDateCarbon = Carbon::parse($this->payload->due_date, SanfCoreApiClientV2::DEFAULT_TIMEZONE);
        $dueDate = $dueDateCarbon->format('Y-m-d');
        $dueDateTimestamp = $dueDateCarbon->timestamp;

        $user = $userRepository->find([
            'xid' => $userProfileXid,
        ]);

        $title = __('Tagihan Anda Jatuh Tempo Hari Ini');
        $subtitle = __('Informasi');
        $body = "Tagihan Anda dengan nilai {$totalAmountCurrency} jatuh tempo hari ini. Segera lakukan pembayaran sebelum terkena biaya keterlambatan.";
        $bodyHtml = "<span>Tagihan Anda dengan nilai <b>{$totalAmountCurrency}</b> jatuh tempo hari ini. Segera lakukan pembayaran sebelum terkena biaya keterlambatan.</span>";

        $webPartnerBaseUrl = rtrim(config('web-partner.base_url'), '/');
        $webPartnerUrl = "{$webPartnerBaseUrl}/sanfind_users/profiles/{$userProfileXid}/plafonds/sparepart/bills/{$contractNo}/{$dueDate}/show";

        $dto = new AddPushNotificationUserRequestDto([
            'user_id' => $user->id,
            'payload' => new AddPushNotificationUserPayloadRequestDto([
                'xid' => nano_id_alphanumeric(21),
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
                'type' => (string) NotificationTypeEnum::REMINDER,
                'screen' => "bill-detail|{$contractNo}|{$dueDateTimestamp}",
                'published_at' => Carbon::now(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'link' => $webPartnerUrl,
            ]),
            'dashboardNotification' => new AddPushNotificationUserDashboardRequestDto([
                'xid' => nano_id_alphanumeric(21),
                'notifiable_type' => PushNotificationUserNotifiableTypeEnum::CUSTOMER_ID,
                'notifiable_id' => $userProfileXid,
                'body' => $bodyHtml,
                'url' => $webPartnerUrl,
                'title' => $title,
            ]),
        ]);

        $service->execute($dto);
    }
}
