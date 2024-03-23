<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

class EloquentProductCartSpecification implements ProductCartSpecificationInterface
{
    public function listByUser($parameter)
    {
        return new EloquentGetProductCartByUserSpecification($parameter);
    }
}
