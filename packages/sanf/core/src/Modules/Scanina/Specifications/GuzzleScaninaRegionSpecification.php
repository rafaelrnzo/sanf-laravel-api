<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

class GuzzleScaninaRegionSpecification implements ScaninaRegionSpecificationInterface
{

    /**
     * @param $parameter
     * @return GuzzleGetCountrySpecification
     */
    public function getCountries($parameter): GuzzleGetCountrySpecification
    {
        return new GuzzleGetCountrySpecification($parameter);
    }

    /**
     * @param $parameter
     * @return GuzzleGetCitySpecification
     */
    public function getCities($parameter): GuzzleGetCitySpecification
    {
        return new GuzzleGetCitySpecification($parameter);
    }

    /**
     * @param $parameter
     * @return GuzzleGetBusinessSectorSpecification
     */
    public function getBusinessSector($parameter): GuzzleGetBusinessSectorSpecification
    {
        return new GuzzleGetBusinessSectorSpecification($parameter);
    }
}
