<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Plafond\UseCases\SendNotificationPlafondDisbursementSubmittedForCustomerUseCase;

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

    public function handle(SendNotificationPlafondDisbursementSubmittedForCustomerUseCase $useCase)
    {
        return $useCase->execute($this->dto);
    }
}
