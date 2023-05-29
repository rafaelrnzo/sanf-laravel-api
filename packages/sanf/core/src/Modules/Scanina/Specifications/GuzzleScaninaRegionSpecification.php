<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductFilterRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductServiceRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSparePartRequestDto;

class GuzzleScaninaRegionSpecification implements ScaninaRegionSpecificationInterface
{

    /**
     * @param $parameter
     * @return GuzzleGetCitySpecification
     */
    public function getCities($parameter): GuzzleGetCitySpecification
    {
        return new GuzzleGetCitySpecification($parameter);
    }
}
