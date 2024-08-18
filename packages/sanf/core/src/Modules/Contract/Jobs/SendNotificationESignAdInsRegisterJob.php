<?php

namespace Sanf\Core\Modules\Contract\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Contract\Services\SendNotificationESignAdInsService;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;

class SendNotificationESignAdInsRegisterJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle(SendNotificationESignAdInsService $useCase)
    {
        $dto = [
            'userId' => $this->userId,
            'payload' => [
                'xid' => nano_id(),
                'title' => __('Pendaftaran Tanda Tangan Digital Berhasil'),
                'subtitle' => '',
                'body' => __('Data diri yang anda kirimkan berhasil di verifikasi. Saat  ini Anda sudah mendapatkan akses untuk melakukan tanda tangan digital di halaman Ttd Kontrak.'),
                'type' => (string) NotificationTypeEnum::INFO,
                'screen' => '',
                'published_at' => Carbon::now(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        return $useCase->execute($dto);
    }
}
