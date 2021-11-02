<?php


namespace Sanf\Core\Modules\Financing\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class FinancingObjectDto extends DataTransferObject
{
    public string $amount;
    public string $providerName;
    public string $brandId;
    public string $brandName;
    public string $typeId;
    public string $typeName;
    public string $modelId;
    public string $modelName;
}
