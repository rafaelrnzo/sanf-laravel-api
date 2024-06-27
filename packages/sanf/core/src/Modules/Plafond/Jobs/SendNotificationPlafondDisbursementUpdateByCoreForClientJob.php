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
            $title = __('Pencairan Plafond Anda Berhasil');
            $subtitle = __('Sukses pencairan plafond');
            $body = __('Pengajuan pencairan plafond Anda telah disetujui Admin SANFIND.');
        } else {
            $title = __('Pencairan Plafond Anda ditolak');
            $subtitle = __('Gagal pencairan plafond');
            $body = __('Pengajuan pencairan plafond Anda telah ditolak Admin SANFIND.');
        }

        $dto = [
            'userId' => $this->dto->userId ?? null,
            'payload' => [
                'xid' => nano_id(),
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
                'type' => (string) NotificationTypeEnum::INFO,
                'screen' => '',
                'published_at' => Carbon::now(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        return $useCase->execute($dto);
    }
}
