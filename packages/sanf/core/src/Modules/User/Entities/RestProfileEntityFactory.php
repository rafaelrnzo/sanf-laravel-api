<?php


namespace Sanf\Core\Modules\User\Entities;


final class RestProfileEntityFactory
{
    public function make(array $attributes = []): ProfileEntityInterface
    {
        return new RestProfileEntity($attributes);
    }
}
