<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\AddToCartRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaUserRegisterRequestDto;

class EloquentProductCartSpecification implements ProductCartSpecificationInterface
{

    public function listByUser($parameter)
    {
        return new EloquentGetProductCartByUserSpecification($parameter);
    }
}
