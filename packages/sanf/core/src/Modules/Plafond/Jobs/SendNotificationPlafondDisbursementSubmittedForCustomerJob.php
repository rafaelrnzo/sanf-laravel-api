<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Plafond\UseCases\SendNotificationPlafondDisbursementForCustomerUseCase;

class SendNotificationPlafondDisbursementSubmittedForCustomerJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $dto;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dto)
    {
        $this->dto = $dto;
    }

    public function handle(SendNotificationPlafondDisbursementForCustomerUseCase $useCase)
    {
        $webPartnerUrl = config('web-partner.base_url') . "plafond/disbursements/{$this->dto->disbursementXid}/submissions/{$this->dto->submissionXid}";
        $body = "<span><b>{$this->dto->client}</b> telah melakukan pengajuan sejumlah {$this->dto->totalAmount} dan membutuhkan review Anda. Periksa sekarang!</span>";
        $notificationData = [
            'xid' => nano_id(),
            'notifiable_type' => 'bowheer_id',
            'notifiable_id' => $this->dto->bowheerId,
            'body' => $body,
            'url' => "{$webPartnerUrl}",
        ];

        $payloadNotification = [
            'xid' => nano_id(),
            'title' => __('Pengajuan Pencairan Plafond'),
            'subtitle' => __('Pengajuan Pencairan plafond'),
            'body' => strip_tags($body),
            'type' => (string) NotificationTypeEnum::INFO,
            'screen' => '',
            'published_at' => Carbon::now(),
            'click_action' => "url:{$webPartnerUrl}",
        ];

        $notificationDto = [
            'bowheerId' => $this->dto->bowheerId,
            'notificationData' => $notificationData,
            'payloadNotification' => $payloadNotification,
        ];

        return $useCase->execute($notificationDto);
    }
}
