<?php

namespace Sanf\Core\Modules\Scanina\Repositories;

use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleScaninaRegionRepository implements ScaninaRegionRepositoryInterface
{
    private ScaninaApiClient $client;

    public function __construct(ScaninaApiClient $client)
    {

        $this->client = $client;
    }

    public function get($specification)
    {
        return $specification->send($this->client);
    }
}
