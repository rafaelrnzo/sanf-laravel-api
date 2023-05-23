<?php

namespace Sanf\Core\Modules\Scanina\Repositories;

use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleScaninaUserRepository implements ScaninaUserRepositoryInterface
{

    private ScaninaApiClient $client;

    public function __construct(ScaninaApiClient $client)
    {

        $this->client = $client;
    }

    public function post($specification)
    {
        return $specification->send($this->client);
    }
}
