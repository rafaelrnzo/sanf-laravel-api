<?php

namespace Sanf\Core\Modules\Payment\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserDashboardRequestDto;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserPayloadRequestDto;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserRequestDto;
use Sanf\Core\Modules\Notification\Enums\PushNotificationUserNotifiableTypeEnum;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Notification\Services\AddPushNotificationUserService;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

class SendPushNotificationPaymentCreatedJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    protected PaymentModel $payment;

    public function __construct(PaymentModel $payment)
    {
        $this->payment = $payment;
    }

    public function handle(
        AddPushNotificationUserService $service
    ) {
        $totalAmount = $this->payment->amount;
        $userAuthId = $this->payment->user_auth_id;
        $userProfileXid = $this->payment->user_profile_xid;
        $totalAmountCurrency = format_currency($totalAmount);

        /**
         * @var InstallmentModel
         */
        $installment = $this->payment->installments->first();

        $installmentContractNo = optional($installment)->contract_no;
        $installmentDueDate = optional($installment)->due_date;
        $installmentDueDateTimestamp = optional($installmentDueDate)->timestamp;

        $title = __('Selesaikan Pembayaran');
        $subtitle = __('Transaksi');
        $body = "Anda memiliki pembayaran sebesar {$totalAmountCurrency} yang perlu diselesaikan. Segera lakukan pembayaran sebelum batas waktu berakhir.";
        $bodyHtml = "<span>Anda memiliki pembayaran sebesar <b>{$totalAmountCurrency}</b> yang perlu diselesaikan. Segera lakukan pembayaran sebelum batas waktu berakhir.</span>";

        $webPartnerBaseUrl = rtrim(config('web-partner.base_url'), '/');
        $webPartnerUrl = "{$webPartnerBaseUrl}/sanfind_users/profiles/{$userProfileXid}/plafonds/sparepart/bills";

        $dto = new AddPushNotificationUserRequestDto([
            'user_id' => $userAuthId,
            'payload' => new AddPushNotificationUserPayloadRequestDto([
                'xid' => nano_id_alphanumeric(21),
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
                'type' => (string) NotificationTypeEnum::INFO,
                'screen' => "bill-detail|{$installmentContractNo}|{$installmentDueDateTimestamp}",
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
