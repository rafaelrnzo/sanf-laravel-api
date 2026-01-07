<?php

namespace Sanf\Core\Modules\Payment\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Sanf\Core\Modules\Payment\Events\PaymentExpiredEvent;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\UseCases\MakePaymentExpireUseCase;

class PaymentExpireJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    protected PaymentModel $payment;

    public function __construct(PaymentModel $payment)
    {
        $this->payment = $payment;
    }

    public function handle(
        MakePaymentExpireUseCase $useCase
    ) {
        $useCase->execute($this->payment->xid);

        event(new PaymentExpiredEvent($this->payment));
    }
}
