<?php

namespace Sanf\Api\Modules\Location\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class GetListSubDistrictDto extends DataTransferObject
{
    public string $province_id;

    public string $city_id;

    public string $district_name;
}
