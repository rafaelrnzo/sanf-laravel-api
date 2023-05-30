<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

interface ScaninaRegionSpecificationInterface
{
    public function getCountries($parameter);
    public function getCities($parameter);
    public function getBusinessSector($parameter);
}
