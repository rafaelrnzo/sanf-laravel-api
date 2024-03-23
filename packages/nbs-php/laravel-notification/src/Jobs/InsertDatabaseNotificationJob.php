<?php

namespace NbsPhp\Notification\Jobs;

use NbsPhp\Core\AbstractJob;
use NbsPhp\Notification\Models\UserNotificationModel;

class InsertDatabaseNotificationJob extends AbstractJob
{
    protected $notifiable;
    protected $payload;

    /**
     * Create a new job instance.
     * @param $notifiable
     * @param $payload
     * @return void
     */
    public function __construct($notifiable, $payload)
    {
        $this->notifiable = $notifiable;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //TODO REPOSITORY
        UserNotificationModel::forceCreate([
            'id_ext' => $this->payload['id_ext'],
            'type' => $this->payload['type'],
            'notifiable_id' => $this->notifiable->id,
            'notifiable_type' => $this->notifiable->type,
            'data' => $this->payload,
        ]);
    }
}
