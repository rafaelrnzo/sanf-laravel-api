<?php

namespace NbsPhp\ApiWrapper\Resource\Contracts;

use GuzzleHttp\Client as GuzzleClient;
use NbsPhp\ApiWrapper\Resource\ApiResource as ApiResource;

interface Update
{
    /**
     * @param int|string $id
     * @param array $attributes
     * @param array $options
     * @param GuzzleClient|null $client
     * @return static
     */
    public static function update(
        $id,
        array $attributes,
        array $options = [],
        GuzzleClient $client = null
    ): ApiResource;

    /**
     * Call the "Update" Route for this ApiResource using an array of attributes.
     * @param array $attributes
     * @param array $options
     * @param GuzzleClient|null $client
     * @return $this
     */
    public function updateAttributes(array $attributes, array $options = [], GuzzleClient $client = null): ApiResource;

    /**
     * Call the "Update" Route for this ApiResource using the instance attributes.
     * @param array $options
     * @param GuzzleClient|null $client
     * @return $this
     */
    public function saveChanges(array $options = [], GuzzleClient $client = null): ApiResource;
}
