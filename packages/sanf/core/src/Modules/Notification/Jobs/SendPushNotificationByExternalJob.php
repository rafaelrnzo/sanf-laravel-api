<?php

namespace Sanf\Core\Modules\Notification\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationByExternalRequestDto;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Notification\Services\AddPushNotificationByExternalService;

class SendPushNotificationByExternalJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected object $content;


    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct(object $content)
    {
        $this->content = $content;
    }

    public function handle(AddPushNotificationByExternalService $service)
    {
        return $service->execute($this->content);
    }
}
