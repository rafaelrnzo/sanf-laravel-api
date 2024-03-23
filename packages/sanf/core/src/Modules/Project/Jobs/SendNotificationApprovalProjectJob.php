<?php

namespace Sanf\Core\Modules\Project\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationByExternalRequestDto;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Notification\Services\AddPushNotificationByExternalService;

class SendNotificationApprovalProjectJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $project;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($project, $user)
    {
        $this->project = $project;
        $this->user = $user;
    }

    public function handle(AddPushNotificationByExternalService $service)
    {
        $dto = new AddPushNotificationByExternalRequestDto([
            'id' => nano_id(),
            'type' => new NotificationTypeEnum(NotificationTypeEnum::INFO),
            'email' => $this->user->username,
            'customer_id' => (string) $this->project->user_id,
            'title' => __('Pengajuan Proyek Anda di SETUJUI'),
            'subtitle' => __('Info Proyek Anda'),
            'screen' => 'my_project',
            'body' => __('Selamat Proyek anda yang berjudul :title telah dipublish', ['title' => $this->project->title]),
            'published_at' => Carbon::now()->timestamp,
        ]);

        return $service->execute($dto);
    }
}
