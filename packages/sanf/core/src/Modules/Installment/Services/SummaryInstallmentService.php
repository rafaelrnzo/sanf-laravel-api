<?php

namespace Sanf\Core\Modules\Installment\Services;

use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentSummaryEntity;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class SummaryInstallmentService
{
    protected SanfCoreApiClientV2 $sanfCoreApiClient;

    public function __construct(
        SanfCoreApiClientV2 $sanfCoreApiClient
    ) {
        $this->sanfCoreApiClient = $sanfCoreApiClient;
    }

    public function execute(): ?SanfCoreInstallmentSummaryEntity
    {
        return $this->sanfCoreApiClient->getInstallmentSummary();
    }
}
