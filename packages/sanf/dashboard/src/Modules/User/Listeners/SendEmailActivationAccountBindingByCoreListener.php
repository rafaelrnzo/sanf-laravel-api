<?php

namespace Sanf\Dashboard\Modules\User\Listeners;

use Sanf\Dashboard\Modules\User\Jobs\SendEmailActivationAccountBindingByCoreJob;

class SendEmailActivationAccountBindingByCoreListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $content = $event->content;

        $payload = [
            'fullName' => $content->fullName,
            'verifyUrl' => config('web-partner.base_url') . "user-verification/{$content->token}",
            'expireInDays' => $content->expireInDays,
        ];

        dispatch(new SendEmailActivationAccountBindingByCoreJob($content->email, $payload));
    }
}
