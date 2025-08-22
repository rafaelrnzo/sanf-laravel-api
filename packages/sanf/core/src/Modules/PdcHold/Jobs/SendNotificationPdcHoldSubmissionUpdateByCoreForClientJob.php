<?php

namespace Sanf\Core\Modules\PdcHold\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;
use Sanf\Core\Modules\PdcHold\Services\SendNotificationPdcHoldSubmissionUpdateForClientService;

/**
 * @since CR2025
 */
class SendNotificationPdcHoldSubmissionUpdateByCoreForClientJob implements ShouldQueue
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

    public function handle(SendNotificationPdcHoldSubmissionUpdateForClientService $service)
    {
        $subtitle = 'No Pengajuan ' . $this->dto->pdcHoldXid;
        if ($this->dto->statusId === PdcHoldStatusEnum::ACCEPTED) {
            $status = 'approved';
            if ($this->dto->type == PdcHoldTypeEnum::RESUME) {
                $title = __('Pengajuan Lanjutkan PDC Disetujui');
                $body = 'Pengajuan lanjutkan PDC pembiayaan Anda telah disetujui oleh SANFind, Silakan cek untuk melihat detailnya.';
            } else {
                $title = __('Pengajuan Hold PDC Disetujui');
                $body = 'Pengajuan hold PDC pembiayaan Anda telah disetujui oleh SANFind, Silakan cek untuk melihat detailnya.';
            }
        } elseif ($this->dto->statusId === PdcHoldStatusEnum::REJECTED) {
            $status = 'rejected';
            if ($this->dto->type == PdcHoldTypeEnum::RESUME) {
                $title = __('Pengajuan Lanjutkan PDC Ditolak');
                $body = 'Mohon maaf, pengajuan lanjutkan PDC pembiayaan Anda belum disetujui. Silakan menghubungi tim SANFind untuk informasi lebih lanjut.';
            } else {
                $title = __('Pengajuan Hold PDC Ditolak');
                $body = 'Mohon maaf, pengajuan hold PDC pembiayaan Anda belum disetujui. Silakan menghubungi tim SANFind untuk informasi lebih lanjut.';
            }
        } else {
            return;
        }

        $dto = [
            'userId' => $this->dto->userId ?? null,
            'payload' => [
                'xid' => nano_id(),
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
                'type' => (string) NotificationTypeEnum::INFO,
                'screen' => "pdc-submission-{$status}|{$this->dto->pdcHoldXid}",
                'published_at' => Carbon::now(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        return $service->execute($dto);
    }
}
