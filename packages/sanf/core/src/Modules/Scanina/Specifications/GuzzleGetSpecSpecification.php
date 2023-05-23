<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetSpecSpecification
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
        return $client->getSpecification($this->xid, $this->type);
    }
}
