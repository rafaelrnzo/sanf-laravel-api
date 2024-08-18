<?php

namespace Sanf\Core\Modules\Contract\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Contract\Services\SendNotificationESignAdInsService;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;

class SendNotificationESignDocumentSignCompleteJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $userId;
    protected string $documentName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $userId, string $documentName)
    {
        $this->userId = $userId;
        $this->documentName = $documentName;
    }

    public function handle(SendNotificationESignAdInsService $useCase)
    {
        $dto = [
            'userId' => $this->userId,
            'payload' => [
                'xid' => nano_id(),
                'title' => __('Tanda Tangan Dokumen Kontrak Selesai'),
                'subtitle' => '',
                'body' => __("Dokumen kontrak {$this->documentName} sudah selesai di tanda tangani oleh semua pihak, silahkan cek untuk lebih detil di halaman Ttd Kontrak."),
                'type' => (string) NotificationTypeEnum::INFO,
                'screen' => '',
                'published_at' => Carbon::now(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        return $useCase->execute($dto);
    }
}
