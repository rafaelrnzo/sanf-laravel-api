<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetCountrySpecification
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
        return $client->getCountry((object) $this->parameter);
    }
}
