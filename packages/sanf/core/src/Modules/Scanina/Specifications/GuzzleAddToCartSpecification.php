<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\AddToCartRequestDto;
use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleAddToCartSpecification
{
    private AddToCartRequestDto $parameter;

    /**
     * @param AddToCartRequestDto $parameter
     */
    public function __construct(AddToCartRequestDto $parameter)
    {
        $this->parameter = $parameter;
    }

    public function send(ScaninaApiClient $client)
    {
        return $client->addToCart($this->parameter);
    }
}
