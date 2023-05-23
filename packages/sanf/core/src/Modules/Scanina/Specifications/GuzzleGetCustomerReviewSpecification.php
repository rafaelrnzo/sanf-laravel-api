<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetCustomerReviewSpecification
{
    private string $xid;
    private int $type;

    /**
     * @param string $xid
     */
    public function __construct(string $xid, int $type)
    {
        $this->xid = $xid;
        $this->type = $type;
    }

    public function send(ScaninaApiClient $client)
    {
        return $client->getCustomerReview($this->xid, $this->type);
    }
}
