<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

final class DisbursementAllocationFormRequest extends CamelCaseDataTransferObject
{
    public string $id;
    public string $name;
    public string $provider;
    public string $accountNo;
    public ?string $notes;
    public bool $isDefault;
    public float $amount;
    public int $orderNo;
}
