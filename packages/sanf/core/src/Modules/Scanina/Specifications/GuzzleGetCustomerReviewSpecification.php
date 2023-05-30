<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetCustomerReviewSpecification
{
    private object $parameter;

    /**
     * @param string $xid
     */
    public function __construct(object $parameter)
    {
        $this->parameter = $parameter;
    }

    public function send(ScaninaApiClient $client)
    {
        return $client->getCustomerReview($this->parameter);
    }
}
