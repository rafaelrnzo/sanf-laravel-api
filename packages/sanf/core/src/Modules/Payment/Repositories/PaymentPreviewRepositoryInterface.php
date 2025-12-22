<?php

namespace Sanf\Core\Modules\Payment\Repositories;

use Sanf\Core\Modules\Payment\Models\PaymentPreviewModel;

interface PaymentPreviewRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $data);

    public function find(array $filters): ?PaymentPreviewModel;
}
