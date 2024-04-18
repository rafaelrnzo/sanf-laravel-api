<?php

namespace Sanf\Core\Modules\Plafond\Entities;

final class PlafondEntityFactoringFactory
{
    public function make(array $attributes = []): GuzzlePlafondFactoringEntity
    {
        return new GuzzlePlafondFactoringEntity($attributes);
    }
}
