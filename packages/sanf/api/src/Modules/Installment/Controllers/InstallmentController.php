<?php

namespace Sanf\Api\Modules\Installment\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Installment\Services\SummaryInstallmentService;

final class InstallmentController extends RestApiController
{
    public function summary(SummaryInstallmentService $service)
    {
        $summary = $service->execute();

        return $this->responseOk('Success', [
            'installment_count' => optional($summary)->jumlah_tagihan ?? 0,
            'total_amount' => optional($summary)->total_tagihan ?? 0,
        ]);
    }
}
