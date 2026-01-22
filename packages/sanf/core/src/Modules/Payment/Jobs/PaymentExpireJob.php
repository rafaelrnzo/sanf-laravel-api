<?php

namespace Sanf\Core\Modules\Payment\Jobs;

use App\Jobs\Job;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Modules\Payment\Events\PaymentExpiredEvent;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\UseCases\MakePaymentExpireUseCase;

class PaymentExpireJob extends Job
{
    protected PaymentModel $payment;

    public function __construct(PaymentModel $payment)
    {
        $this->payment = $payment;
    }

    public function handle(
        MakePaymentExpireUseCase $useCase
    ) {
        DB::transaction(fn () => $useCase->execute($this->payment->xid));

        event(new PaymentExpiredEvent($this->payment));
    }

    public function backoff()
    {
        // waits 60s, then 120s, then 240s, etc.
        return 60 * (2 ** ($this->attempts() - 1));
    }
}
