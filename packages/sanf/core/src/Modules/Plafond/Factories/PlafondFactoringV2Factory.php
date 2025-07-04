<?php

namespace Sanf\Core\Modules\Plafond\Factories;

use Sanf\Core\Modules\Plafond\Entities\GuzzlePlafondFactoringV2Entity;

class PlafondFactoringV2Factory
{
    public function make(array $data): GuzzlePlafondFactoringV2Entity
    {
        return new GuzzlePlafondFactoringV2Entity($data);
    }
}
