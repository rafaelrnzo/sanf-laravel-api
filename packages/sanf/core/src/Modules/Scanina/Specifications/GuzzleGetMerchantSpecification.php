<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetMerchantSpecification
{
    private object $parameter;

    /**
     * @param object $parameter
     */
    public function __construct(object $parameter)
    {
        $this->parameter = $parameter;
    }

    public function send(ScaninaApiClient $client)
    {
        return $client->getMerchant((object) $this->parameter);
    }
}
