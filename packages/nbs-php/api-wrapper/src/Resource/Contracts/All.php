<?php

namespace NbsPhp\ApiWrapper\Resource\Contracts;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Collection;

interface All
{
    /**
     * Call the "All" Route for this Resource.
     * @param array $options
     * @param GuzzleClient|null $client
     * @return Collection
     */
    public static function all(array $options = [], GuzzleClient $client = null): Collection;
}
