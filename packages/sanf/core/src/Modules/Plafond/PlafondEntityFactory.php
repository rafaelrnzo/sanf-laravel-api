<?php


namespace Sanf\Core\Modules\Plafond;


use Sanf\Core\Modules\Plafond\Entities\PlafondEntity;

final class PlafondEntityFactory
{
    public function make(array $attributes = []): PlafondEntity
    {
        return new PlafondEntity($attributes);
    }
}
