<?php

namespace Sanf\Api\Modules\Location\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class GetListCityDto extends DataTransferObject
{
    public string $province_id;
}
