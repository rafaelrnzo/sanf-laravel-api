<?php

namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FinancingObjectDto extends CamelCaseDataTransferObject
{
    public int $amount;
    public string $providerName;
    public string $brandId;
    public string $brandName;
    public string $typeId;
    public string $typeName;
    public string $modelId;
    public string $modelName;
}
