<?php

namespace NbsPhp\Notification\Jobs;

use Illuminate\Mail\Mailer;
use NbsPhp\Core\AbstractJob;
use NbsPhp\Core\Mail\BaseMail;

class SendEmailNotificationJob extends AbstractJob
{
    protected $notifiable;
    protected $payload;

    /**
     * Create a new job instance.
     *
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
    public function handle(Mailer $mail)
    {
        //phpcs:disable
        $mailable = (new BaseMail)
            ->subject($this->payload['title'])
            ->logo(asset('images/logos/klar-lockup-green-tiny.png'))
            ->greeting(__('Hello, ') . $this->notifiable->name)
            ->line($this->payload['body'])
            ->line('<span class="action-help-bottom"><br>Best, KLAR</span>')
            ->to($this->notifiable->email, $this->notifiable->name);
        $mail->send($mailable);
//        //phpcs:enable
    }
}
