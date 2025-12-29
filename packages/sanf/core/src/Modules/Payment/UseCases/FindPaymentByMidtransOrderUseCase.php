<?php

namespace Sanf\Core\Modules\Payment\UseCases;

use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Repositories\PaymentRepositoryInterface;

final class FindPaymentByMidtransOrderUseCase
{
    protected PaymentRepositoryInterface $repository;

    public function __construct(
        PaymentRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(string $midtransOrderId): ?PaymentModel
    {
        return $this->repository->findByMidtransOrder($midtransOrderId);
    }
}
