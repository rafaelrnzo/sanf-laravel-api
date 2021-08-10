<?php

namespace NbsPhp\ApiWrapper\Resource\Operations;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Collection;
use NbsPhp\ApiWrapper\Api\Request;
use Symfony\Component\String\Inflector\EnglishInflector;
use function Symfony\Component\String\u;

/**
* @mixin \NbsPhp\ApiWrapper\Resource\ApiResource
 */
trait All
{
    /**
     * Get the name for the "All" Route for this Resource.
     * @return string
     */
    public function allRoute(): string
    {
        $class = explode('\\', static::class);
        $resource = (new EnglishInflector())->pluralize(array_pop($class))[0];

        return $this->allRoute ??
             u($resource)->camel() . '.all';
    }

    /**
     * Call the "All" Route for this Resource.
     * @param array $options
     * @param GuzzleClient|null $client
     * @return Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException
     */
    public static function all(array $options = [], GuzzleClient $client = null): Collection
    {
        $response = Request::route(static::cast([])->allRoute(), $client)
            ->options($options)
            ->send();

        return static::castMany($response->json());
    }
}
