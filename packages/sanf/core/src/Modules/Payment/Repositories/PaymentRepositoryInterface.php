<?php

namespace Sanf\Core\Modules\Payment\Repositories;

use Sanf\Core\Modules\Payment\Models\PaymentModel;

interface PaymentRepositoryInterface
{
    public function find(array $filters): ?PaymentModel;

    public function create(array $data): PaymentModel;

    public function createInstallments(array $filters, array $installments): void;
}
