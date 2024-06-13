<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Plafond\UseCases\SendNotificationPlafondDisbursementSubmittedForClientUseCase;

class SendNotificationPlafondDisbursementSubmittedForClientJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle(SendNotificationPlafondDisbursementSubmittedForClientUseCase $useCase)
    {
        return $useCase->execute($this->userId);
    }
}
