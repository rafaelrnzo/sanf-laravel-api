<?php

namespace NbsPhp\ApiWrapper\Resource\Contracts;

use GuzzleHttp\Client as GuzzleClient;
use NbsPhp\ApiWrapper\Resource\ApiResource as ApiResource;

interface Create
{
    /**
     * @param array $attributes
     * @param array $options
     * @param GuzzleClient|null $client
     * @return static
     */
    public static function create(array $attributes, array $options = [], GuzzleClient $client = null): ApiResource;

    /**
     * Call the "Create" Route for this ApiResource using a constructed instance.
     * @param array $options
     * @param GuzzleClient|null $client
     * @return $this
     */
    public function store(array $options = [], GuzzleClient $client = null): ApiResource;
}
