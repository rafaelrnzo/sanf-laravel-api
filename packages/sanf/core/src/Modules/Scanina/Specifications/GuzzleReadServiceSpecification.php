<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleReadServiceSpecification
{
    private string $xid;

    /**
     * @param string $xid
     */
    public function __construct(string $xid)
    {
        $this->xid = $xid;
    }

    public function send(ScaninaApiClient $client)
    {
        return $client->readService($this->xid);
    }
}
