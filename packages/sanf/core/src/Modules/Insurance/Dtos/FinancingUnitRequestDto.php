<?php

namespace Sanf\Core\Modules\Insurance\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FinancingUnitRequestDto extends CamelCaseDataTransferObject
{
    public string $polisNo;
    public string $serialNo;
    public string $brandTypeModel;
    public string $year;
}
