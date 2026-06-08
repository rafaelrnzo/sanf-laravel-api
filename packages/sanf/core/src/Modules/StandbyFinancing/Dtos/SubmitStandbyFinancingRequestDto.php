<?php

namespace Sanf\Core\Modules\StandbyFinancing\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class SubmitStandbyFinancingRequestDto extends CamelCaseDataTransferObject
{
    public ?string $custId;
    public string $noPlafond;
    public string $periodStart;
    public string $periodEnd;
    public int $tenor;
    public array $supplier;
    public array $bankAccount;
    public ?array $documents;
    public ?array $dokuments;
    public ?string $paymentMethod;
    public ?string $sourceChannel;
    public ?bool $agreementCheckbox;
    public ?string $requestId;
}
