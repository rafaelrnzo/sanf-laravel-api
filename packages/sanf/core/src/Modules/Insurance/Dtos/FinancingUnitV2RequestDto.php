<?php

namespace Sanf\Core\Modules\Insurance\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class FinancingUnitV2RequestDto extends CamelCaseDataTransferObject
{
    public string $polisNo;
    public string $serialNo;
    public string $brandTypeModel;
    public string $year;
    public ?string $cityId;
    public ?string $cityName;
    public string $emailProvider;
    public ?string $emailCc;
}
