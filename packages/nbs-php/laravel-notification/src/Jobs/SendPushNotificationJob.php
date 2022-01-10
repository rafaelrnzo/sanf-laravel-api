<?php

namespace NbsPhp\Notification\Jobs;

use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Core\AbstractJob;
use NbsPhp\Notification\Models\UserSessionModel;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;

class SendPushNotificationJob extends AbstractJob
{
    protected $token;
    protected $payload;

    /**
     * SendPushNotificationJob constructor.
     * @param $token
     * @param $payload
     */
    public function __construct($token, $payload)
    {
        $this->token = $token;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PushNotificationServiceInterface $service)
    {
        try {
            $service->sendToDevice($this->token, $this->payload);
        } catch (MessagingException $e) {
            UserSessionModel::where('notification_token', $this->token)->delete();
            report($e);
        }
    }
}
