<?php

namespace NbsPhp\ApiWrapper\Resource\Operations;

use GuzzleHttp\Client as GuzzleClient;
use NbsPhp\ApiWrapper\Api\Request;
use Symfony\Component\String\Inflector\EnglishInflector;
use function Symfony\Component\String\u;

/**
* @mixin \NbsPhp\ApiWrapper\Resource\ApiResource
 */
trait Delete
{
    /**
     * Get the name for the "Delete" Route for this Resource.
     * @return string
     */
    public function deleteRoute(): string
    {
        $class = explode('\\', static::class);
        $resource = (new EnglishInflector())->pluralize(array_pop($class))[0];

        return $this->deleteRoute ??
            u($resource)->camel() . '.delete';
    }

    /**
     * Call the "Delete" Route for this Resource.
     * @param string|int $id
     * @param array $options
     * @param GuzzleClient|null $client
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException
     */
    public static function delete($id, array $options = [], GuzzleClient $client = null): void
    {
        $instance = static::cast([]);
        $instance->setAttribute($instance->getIdField(), $id);
        $instance->destroy($options, $client);
    }

    /**
     * Call the "Delete" Route for this ApiResource instance.
     * @param array $options
     * @param GuzzleClient|null $client
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException
     */
    public function destroy(array $options = [], GuzzleClient $client = null): void
    {
        Request::route($this->deleteRoute(), $client)
            ->pathParams([$this->getIdField() => $this->getId()])
            ->options($options)
            ->send();
    }
}
