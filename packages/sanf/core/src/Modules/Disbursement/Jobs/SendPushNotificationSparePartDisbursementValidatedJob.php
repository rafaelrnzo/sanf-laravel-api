<?php

namespace Sanf\Core\Modules\Disbursement\Jobs;

use App\Jobs\Job;
use Sanf\Core\Modules\Notification\Messages\SparePartDisbursementValidationCompletedMessage;
use Sanf\Core\Modules\Notification\Services\AddPushNotificationPartnerService;

class SendPushNotificationSparePartDisbursementValidatedJob extends Job
{
    protected string $bowheerId;
    protected string $batchNumber;

    public function __construct(string $bowheerId, string $batchNumber)
    {
        $this->bowheerId = $bowheerId;
        $this->batchNumber = $batchNumber;
    }

    public function handle(
        AddPushNotificationPartnerService $service
    ) {
        $dto = SparePartDisbursementValidationCompletedMessage::make(
            $this->bowheerId,
            $this->batchNumber
        );

        $service->execute($dto);
    }
}
