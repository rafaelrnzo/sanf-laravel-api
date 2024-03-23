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
trait Get
{
    /**
     * Get the name for the "Get" Route for this Resource.
     * @return string
     */
    public function getRoute(): string
    {
        $class = explode('\\', static::class);
        $resource = (new EnglishInflector())->pluralize(array_pop($class))[0];

        return $this->getRoute ??
            u($resource)->camel() . '.get';
    }

    /**
     * Call the "Get" Route for this Resource.
     * @param string|int $id
     * @param array $options
     * @param GuzzleClient|null $client
     * @return static|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException
     */
    public static function get($id, array $options = [], GuzzleClient $client = null): ?ApiResource
    {
        $response = Request::route(static::cast([])->getRoute(), $client)
            ->pathParams([static::cast([])->getIdField() => $id])
            ->options($options)
            ->send();

        return static::cast($response->json());
    }

    /**
     * @param array $options
     * @param GuzzleClient|null $client
     * @return static|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException
     */
    public function fresh(array $options = [], GuzzleClient $client = null): ?ApiResource
    {
        return static::get($this->getId(), $options, $client);
    }

    /**
     * @param array $options
     * @param GuzzleClient|null $client
     * @return $this
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException
     */
    public function refresh(array $options = [], GuzzleClient $client = null): ApiResource
    {
        $response = Request::route($this->getRoute(), $client)
            ->pathParams([$this->getIdField() => $this->getId()])
            ->options($options)
            ->send();

        return $this->setAttributes($response->json(), true);
    }
}
