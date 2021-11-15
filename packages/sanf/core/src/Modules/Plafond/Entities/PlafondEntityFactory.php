<?php


namespace Sanf\Core\Modules\Plafond\Entities;


final class PlafondEntityFactory
{
    public function make(array $attributes = []): GuzzlePlafondEntity
    {
        return new GuzzlePlafondEntity($attributes);
    }
}
