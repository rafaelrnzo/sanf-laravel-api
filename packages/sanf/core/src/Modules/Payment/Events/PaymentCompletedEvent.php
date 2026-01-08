<?php

namespace Sanf\Core\Modules\Payment\Events;

use NbsPhp\Core\Event;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

class PaymentCompletedEvent extends Event
{
    public $payment;

    public function __construct(PaymentModel $payment)
    {
        $this->payment = $payment;
    }
}
