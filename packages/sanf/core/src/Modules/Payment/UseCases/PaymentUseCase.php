<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Illuminate\Support\Carbon;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;

final class PaymentUseCase
{
    protected PaymentRepositoryInterface $repository;

    public function __construct(
        PaymentRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function refreshLastStatusCheck(string $xid): bool
    {
        return $this->repository->updatePayment(
            ['xid' => $xid],
            ['last_checked_status_at' => Carbon::now()]
        );
    }
}
