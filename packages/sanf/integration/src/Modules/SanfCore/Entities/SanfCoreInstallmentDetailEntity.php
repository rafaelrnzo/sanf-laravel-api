<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreInstallmentDetailEntity extends FlexibleDataTransferObject
{
    public SanfCoreInstallmentDetailContractEntity $kontrak;

    public SanfCoreInstallmentDetailBillEntity $tagihan;

    /** @var \Sanf\Integration\Modules\SanfCore\Entities\SanfCoreInstallmentDetailOverdueEntity[]|null */
    public ?array $overdue;

    public ?SanfCoreInstallmentDetailEStatementEntity $e_statement;
}
