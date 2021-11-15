<?php


namespace Sanf\Core\Modules\Plafond\Entities;


final class PlafondEntityHistoryFactory
{
    public function make(array $attributes = []): GuzzlePlafondHistoryEntity
    {
        return new GuzzlePlafondHistoryEntity($attributes);
    }
}
