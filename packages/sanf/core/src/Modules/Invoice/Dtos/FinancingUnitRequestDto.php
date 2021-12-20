<?php

namespace Sanf\Core\Modules\Invoice\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FinancingUnitRequestDto extends CamelCaseDataTransferObject
{
    public string $contractNo;
    public string $serialNo;
    public string $brandTypeModel;
    public string $year;
}
