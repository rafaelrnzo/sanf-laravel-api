<?php

namespace Sanf\Core\Modules\Payment\Jobs;

use App\Jobs\Job;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Modules\Payment\UseCases\ResubmitInstallmentOfCompletedPaymentUseCase;

class ResubmitPendingInstallmentPaymentJob extends Job
{
    protected string $paymentXid;

    public function __construct(string $paymentXid)
    {
        $this->paymentXid = $paymentXid;
    }

    public function handle(
        ResubmitInstallmentOfCompletedPaymentUseCase $useCase
    ) {
        DB::transaction(
            fn () => $useCase->execute($this->paymentXid)
        );
    }

    public function backoff()
    {
        // waits 60s, then 120s, then 240s, etc.
        return 60 * (2 ** ($this->attempts() - 1));
    }
}
