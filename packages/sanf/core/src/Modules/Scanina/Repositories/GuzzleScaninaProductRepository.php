<?php

namespace Sanf\Core\Modules\Scanina\Repositories;

use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleScaninaProductRepository implements ScaninaProductRepositoryInterface
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

    public function post($specification)
    {
        return $specification->send($this->client);
    }
}
