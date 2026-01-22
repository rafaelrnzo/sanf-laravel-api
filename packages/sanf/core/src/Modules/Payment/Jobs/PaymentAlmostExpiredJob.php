<?php

namespace Sanf\Core\Modules\Payment\Jobs;

use App\Jobs\Job;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Events\PaymentCompletedEvent;
use Sanf\Core\Modules\Payment\Events\PaymentExpiredEvent;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\UseCases\CheckPaymentStatusUseCase;

class PaymentAlmostExpiredJob extends Job
{
    protected PaymentModel $payment;

    public function __construct(PaymentModel $payment)
    {
        $this->payment = $payment;
    }

    public function handle(
        CheckPaymentStatusUseCase $useCase
    ) {
        $payment = $this->payment;

        $newStatus = DB::transaction(fn () => $useCase->execute($payment->xid, $payment->user_auth_id, $payment->user_profile_xid));

        if ($payment->status !== PaymentStatusEnum::PENDING) {
            return;
        }

        if ($newStatus === PaymentStatusEnum::SUCCESS) {
            event(new PaymentCompletedEvent($payment));
        }

        if ($newStatus === PaymentStatusEnum::EXPIRED) {
            event(new PaymentExpiredEvent($payment));
        }
    }

    public function backoff()
    {
        // waits 60s, then 120s, then 240s, etc.
        return 60 * (2 ** ($this->attempts() - 1));
    }
}
