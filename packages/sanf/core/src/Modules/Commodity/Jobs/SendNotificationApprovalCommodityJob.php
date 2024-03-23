<?php

namespace Sanf\Core\Modules\Commodity\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationByExternalRequestDto;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Notification\Services\AddPushNotificationByExternalService;

class SendNotificationApprovalCommodityJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $commodity;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($commodity, $user)
    {
        $this->commodity = $commodity;
        $this->user = $user;
    }

    public function handle(AddPushNotificationByExternalService $service)
    {
        $dto = new AddPushNotificationByExternalRequestDto([
            'id' => nano_id(),
            'type' => new NotificationTypeEnum(NotificationTypeEnum::INFO),
            'email' => $this->user->username,
            'customer_id' => (string) $this->commodity->user_id,
            'title' => __('Pengajuan iklan Komoditas Anda di SETUJUI'),
            'subtitle' => __('Info Iklan Komoditas Anda'),
            'screen' => 'my_commodity',
            'body' => __('Selamat pengajuan iklan komoditas anda yang berjudul :title telah dipublish', ['title' => $this->commodity->title]),
            'published_at' => Carbon::now()->timestamp,
        ]);

        return $service->execute($dto);
    }
}
