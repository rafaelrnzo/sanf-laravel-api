<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Sanf\Core\Modules\Plafond\UseCases\SendNotificationPlafondDisbursementForClientUseCase;

class SendNotificationPlafondDisbursementUpdateByCoreForClientJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $dto;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dto)
    {
        $this->dto = $dto;
    }

    public function handle(SendNotificationPlafondDisbursementForClientUseCase $useCase)
    {

        if ($this->dto->statusId === PlafondDisbursementStatusEnum::APPROVE) {
            $title = __('Pencairan Plafond Disetujui');
            $subtitle = $this->dto->client ?? __('Sukses pencairan plafond');
            $body = "Pengajuan atas nama {$this->dto->bowheer} telah disetujui oleh SANF, Silahkan cek untuk melihat detailnya.";
            $bodyHtml = "<span>Pengajuan atas nama <b>{$this->dto->bowheer}</b> telah <b>disetujui</b> oleh SANF, Silahkan cek untuk melihat detailnya.</span>";
        } else {
            $title = __('Pencairan Plafond Ditolak');
            $subtitle = $this->dto->client ?? __('Gagal pencairan plafond');
            $body = "Pengajuan atas nama {$this->dto->bowheer} telah ditolak oleh SANF, Silahkan cek untuk melihat detailnya.";
            $bodyHtml = "<span>Pengajuan atas nama <b>{$this->dto->bowheer}</b> telah <b>ditolak</b> oleh SANF, Silahkan cek untuk melihat detailnya.</span>";
        }
        // e.g. http://partner.sanf.co.id/sanfind_users/profiles/8624PROSM/plafonds/factoring/2012400518/disbursements/yAg_DPSMCfzFxeSJTru5Z
        $webPartnerUrl = config('web-partner.base_url') . "sanfind_users/{$this->dto->clientId}/plafonds/factoring/{$this->dto->plafondId}/disbursements/{$this->dto->disbursementXid}";

        $dto = [
            'userId' => $this->dto->userId ?? null,
            'payload' => [
                'xid' => nano_id(),
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
                'type' => (string) NotificationTypeEnum::INFO,
                'screen' => "plafond_approved|{$this->dto->plafondId}/{$this->dto->disbursementXid}",
                'published_at' => Carbon::now(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'link' => $webPartnerUrl,
            ],
            'dashboardNotification' => [
                'xid' => nano_id(),
                'notifiable_type' => 'customer_id',
                'notifiable_id' => $this->dto->clientId,
                'body' => $bodyHtml,
                'url' => $webPartnerUrl,
                'title' => $title,
            ],
        ];

        return $useCase->execute($dto);
    }
}
