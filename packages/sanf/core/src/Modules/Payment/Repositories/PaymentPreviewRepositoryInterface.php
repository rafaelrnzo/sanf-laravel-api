<?php

namespace Sanf\Core\Modules\Payment\Repositories;

interface PaymentPreviewRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $data);
}
