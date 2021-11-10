<?php


namespace Sanf\Core\Modules\Plafond;


use Sanf\Core\Modules\Plafond\Entities\GuzzlePlafondEntity;

final class PlafondEntityFactory
{
    public function make(array $attributes = []): GuzzlePlafondEntity
    {
        return new GuzzlePlafondEntity($attributes);
    }
}
