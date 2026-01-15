<?php

namespace Sanf\Core\Modules\Notification\Messages;

use Illuminate\Support\Carbon;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationPartnerRequestDto;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserDashboardRequestDto;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserPayloadRequestDto;
use Sanf\Core\Modules\Notification\Enums\PushNotificationUserNotifiableTypeEnum;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;

final class SparePartDisbursementValidationCompletedMessage
{
    public static function make(string $bowheerId, string $batchNumber)
    {
        $title = __('Pengecekan Data Selesai');
        $body = "Proses pengecekan data invoice dengan ID batch {$batchNumber} telah selesai. Silakan lanjutkan ke tahapan berikutnya.";
        $bodyHtml = "<span>Proses pengecekan data invoice dengan ID batch <b>{$batchNumber}</b> telah selesai. Silakan lanjutkan ke tahapan berikutnya.</span>";

        $webPartnerBaseUrl = rtrim(config('web-partner.base_url'), '/');
        $webPartnerUrl = "{$webPartnerBaseUrl}/plafond/sparepart-submissions/create/{$batchNumber}";

        return new AddPushNotificationPartnerRequestDto([
            'bowheerId' => $bowheerId,
            'payload' => new AddPushNotificationUserPayloadRequestDto([
                'xid' => nano_id_alphanumeric(21),
                'title' => $title,
                'subtitle' => '',
                'body' => $body,
                'type' => (string) NotificationTypeEnum::INFO,
                'screen' => '',
                'published_at' => Carbon::now(),
                'click_action' => "url:{$webPartnerUrl}",
                'link' => $webPartnerUrl,
            ]),
            'dashboardNotification' => new AddPushNotificationUserDashboardRequestDto([
                'xid' => nano_id_alphanumeric(21),
                'notifiable_type' => PushNotificationUserNotifiableTypeEnum::BOWHEER_ID,
                'notifiable_id' => $bowheerId,
                'body' => $bodyHtml,
                'url' => $webPartnerUrl,
                'title' => $title,
            ]),
        ]);
    }
}
