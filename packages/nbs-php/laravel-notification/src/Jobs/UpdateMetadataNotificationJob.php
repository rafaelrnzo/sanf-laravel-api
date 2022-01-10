<?php

namespace NbsPhp\Notification\Jobs;


use Illuminate\Support\Facades\DB;
use NbsPhp\Core\AbstractJob;
use NbsPhp\Notification\Models\UserMetadataModel;

class UpdateMetadataNotificationJob extends AbstractJob
{
    protected $notifiable;
    protected $event;
    protected $metadata;

    /**
     * Create a new job instance.
     * @param $userId
     * @return void
     */
    public function __construct($metadata, $event, $notifiable)
    {
        $this->metadata = $metadata;
        $this->notifiable = $notifiable;
        $this->event = $event;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->metadata as $metadataKey) {
            //TODO USE REPO
            $result = UserMetadataModel::where('user_id', $this->notifiable->id)
                ->where('key', $metadataKey)
                ->update([
                    'value' => DB::raw('version+1'),
                    'version' => DB::raw('version+1')
                ]);
            if (!$result) {
                UserMetadataModel::forceCreate([
                    'user_id' => $this->notifiable->id,
                    'key' => $metadataKey,
                    'value' => 1,
                    'version' => 1,
                ]);
            }
        }

    }
}
