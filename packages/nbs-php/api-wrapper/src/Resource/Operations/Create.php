<?php

namespace NbsPhp\ApiWrapper\Resource\Operations;

use GuzzleHttp\Client as GuzzleClient;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Resource\ApiResource;
use Symfony\Component\String\Inflector\EnglishInflector;
use function Symfony\Component\String\u;

/**
 * @mixin \NbsPhp\ApiWrapper\Resource\ApiResource
 */
trait Create
{
    /**
     * Get the name for the "Create" Route for this Resource.
     * @return string
     */
    public function createRoute(): string
    {
        $class = explode('\\', static::class);
        $resource = (new EnglishInflector())->pluralize(array_pop($class))[0];

        return $this->createRoute ??
            u($resource)->camel() . '.create';
    }

    /**
     * Call the "Create" Route for this ApiResource using an array of attributes.
     * @param array $attributes
     * @param array $options
     * @param GuzzleClient|null $client
     * @return static
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException
     */
    public static function create(array $attributes, array $options = [], GuzzleClient $client = null): ApiResource
    {
        return static::cast($attributes)->store($options, $client);
    }

    /**
     * Call the "Create" Route for this ApiResource using a constructed instance.
     * @param array $options
     * @param GuzzleClient|null $client
     * @return $this
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException
     */
    public function store(array $options = [], GuzzleClient $client = null): ApiResource
    {
        $response = Request::route($this->createRoute(), $client)
            ->json($this->toArray())
            ->options($options)
            ->send();

        return $this->mergeAttributes($response->json() ?? []);
    }
}
