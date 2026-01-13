<?php

namespace Sanf\Core\Modules\Installment\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Sanf\Core\Modules\Installment\Payloads\SendPushNotificationBillChangeJobPayload;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserDashboardRequestDto;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserPayloadRequestDto;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationUserRequestDto;
use Sanf\Core\Modules\Notification\Enums\PushNotificationUserNotifiableTypeEnum;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Notification\Services\AddPushNotificationUserService;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

class SendPushNotificationBillChangeJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    protected SendPushNotificationBillChangeJobPayload $payload;

    public function __construct(SendPushNotificationBillChangeJobPayload $payload)
    {
        $this->payload = $payload;
    }

    public function handle(
        AddPushNotificationUserService $service,
        UserRepositoryInterface $userRepository
    ) {
        $userProfileXid = $this->payload->customer_id_sanfind;
        $contractNo = $this->payload->contract_no;
        $dueDate = Carbon::parse($this->payload->due_date, SanfCoreApiClientV2::DEFAULT_TIMEZONE)->format('Y-m-d');
        $tenorValue = $this->payload->tenor_value;
        $tenorUnit = $this->resolveTenorUnit($this->payload->tenor_unit);

        $user = $userRepository->find([
            'xid' => $userProfileXid,
        ]);

        $title = __('Tagihan Anda Diubah Menjadi Angsuran');
        $subtitle = __("No. Kontrak: {$contractNo}");
        $body = "Karena Anda tidak membayar sampai tanggal jatuh tempo tagihan Anda diubah menjadi angsuran selama {$tenorValue} {$tenorUnit}. Cek detailnya sekarang.";
        $bodyHtml = "<span>Karena Anda tidak membayar sampai tanggal jatuh tempo tagihan Anda diubah menjadi angsuran selama <b>{$tenorValue} {$tenorUnit}</b>. Cek detailnya sekarang.</span>";

        $webPartnerBaseUrl = rtrim(config('web-partner.base_url'), '/');
        $webPartnerUrl = "{$webPartnerBaseUrl}/sanfind_users/profiles/{$userProfileXid}/plafonds/sparepart/bills/{$contractNo}/{$dueDate}/show";

        $dto = new AddPushNotificationUserRequestDto([
            'user_id' => $user->id,
            'payload' => new AddPushNotificationUserPayloadRequestDto([
                'xid' => nano_id_alphanumeric(21),
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
                'type' => (string) NotificationTypeEnum::INFO,
                'screen' => "contract_detail|{$contractNo}",
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

    private function resolveTenorUnit(string $tenorUnit): ?string
    {
        $unit = [
            'day' => 'Hari',
            'month' => 'Bulan',
            'year' => 'Tahun',
        ];

        return $unit[$tenorUnit] ?? null;
    }
}
