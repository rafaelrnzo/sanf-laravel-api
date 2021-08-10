<?php

namespace NbsPhp\ApiWrapper\Resource\Contracts;

use GuzzleHttp\Client as GuzzleClient;
use NbsPhp\ApiWrapper\Resource\ApiResource as ApiResource;

interface Get
{
    /**
     * Call the "Get" Route for this Resource.
     * @param string|int $id
     * @param array $options
     * @param GuzzleClient|null $client
     * @return static|null
     */
    public static function get($id, array $options = [], GuzzleClient $client = null): ?ApiResource;

    /**
     * @param array $options
     * @param GuzzleClient|null $client
     * @return static|null
     */
    public function fresh(array $options = [], GuzzleClient $client = null): ?ApiResource;

    /**
     * @param array $options
     * @param GuzzleClient|null $client
     * @return $this
     */
    public function refresh(array $options = [], GuzzleClient $client = null): ApiResource;
}
