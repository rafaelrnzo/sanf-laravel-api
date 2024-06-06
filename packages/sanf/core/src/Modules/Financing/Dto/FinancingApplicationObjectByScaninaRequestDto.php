<?php

namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FinancingApplicationObjectByScaninaRequestDto extends CamelCaseDataTransferObject
{
    public string $provider;
    public string $category;
    public string $brand;
    public string $type;
    public string $model;
    public ?string $description;
    public int $quantity;
    public float $pricePerUnit;
}
