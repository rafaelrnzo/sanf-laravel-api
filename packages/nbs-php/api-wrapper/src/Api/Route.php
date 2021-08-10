<?php

namespace NbsPhp\ApiWrapper\Api;

use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;

class Route
{
    //region Data
    /**
     * Route Group URLs. Must be an array to track nested groups.
     *
     * @var array
     */
    protected static $urlStack = [];

    /**
     * Route Group Processors. Must be an array to track nested groups.
     *
     * @var array
     */
    protected static $processorStack = [];

    /**
     * Registered Endpoints.
     *
     * @var array
     */
    protected static $endpoints = [];
    //endregion

    //region Groups
    /**
     * Create a Route group, applying the same base URL and Processors to all routes declared inside.
     *
     * @param string|null $baseUrl
     * @param array $processors
     * @param callable|null $callback
     */
    public static function group(string $baseUrl = null, array $processors = [], callable $callback = null)
    {
        static::push($baseUrl, $processors);

        if ($callback) {
            $callback();
        }

        static::pop();
    }

    /**
     * Push Group URL and Processors onto the stack.
     *
     * @param string $baseUrl
     * @param array $processors
     */
    protected static function push(string $baseUrl, array $processors)
    {
        self::$urlStack[] = $baseUrl;
        self::$processorStack[] = $processors;
    }

    /**
     * Pop Group URL and Processors from the stack.
     */
    protected static function pop()
    {
        array_pop(self::$urlStack);
        array_pop(self::$processorStack);
    }

    /**
     * Retrieve the base URL for the current Route Group.
     *
     * @return string
     */
    protected static function groupUrl(): string
    {
        $urlStack = self::$urlStack;
        $top = null;

        while (is_null($top) && count($urlStack) > 0) {
            $top = array_pop($urlStack);
        }

        return $top ?? '';
    }

    /**
     * Retrieve a list of all Processors to apply to the current Route Group.
     *
     * @return array
     */
    protected static function groupProcessors(): array
    {
        return array_merge(...self::$processorStack);
    }
    //endregion

    //region Endpoints
    /**
     * Find the Endpoint with the given name.
     *
     * @param string $name
     * @return Endpoint
     * @throws EndpointNotDefinedException
     */
    public static function find(string $name): Endpoint
    {
        if (array_key_exists($name, static::$endpoints)) {
            return static::$endpoints[$name];
        }

        throw new EndpointNotDefinedException($name);
    }

    /**
     * Create a new Endpoint.
     *
     * @param string $method
     * @param string $name
     * @param string $path
     * @return Endpoint
     */
    public static function endpoint(string $method, string $name, string $path): Endpoint
    {
        $endpoint = (new Endpoint($method, $path))
            ->baseUrl(self::groupUrl())
            ->processor(self::groupProcessors());

        static::$endpoints[$name] = $endpoint;

        return $endpoint;
    }

    /**
     * Create a new OPTIONS Endpoint.
     *
     * @param string $name
     * @param string $path
     * @return Endpoint
     */
    public static function options(string $name, string $path): Endpoint
    {
        return static::endpoint('OPTIONS', $name, $path);
    }

    /**
     * Create a new HEAD Endpoint.
     *
     * @param string $name
     * @param string $path
     * @return Endpoint
     */
    public static function head(string $name, string $path): Endpoint
    {
        return static::endpoint('HEAD', $name, $path);
    }

    /**
     * Create a new GET Endpoint.
     *
     * @param string $name
     * @param string $path
     * @return Endpoint
     */
    public static function get(string $name, string $path): Endpoint
    {
        return static::endpoint('GET', $name, $path);
    }

    /**
     * Create a new POST Endpoint.
     *
     * @param string $name
     * @param string $path
     * @return Endpoint
     */
    public static function post(string $name, string $path): Endpoint
    {
        return static::endpoint('POST', $name, $path);
    }

    /**
     * Create a new PUT Endpoint.
     *
     * @param string $name
     * @param string $path
     * @return Endpoint
     */
    public static function put(string $name, string $path): Endpoint
    {
        return static::endpoint('PUT', $name, $path);
    }

    /**
     * Create a new PATCH Endpoint.
     *
     * @param string $name
     * @param string $path
     * @return Endpoint
     */
    public static function patch(string $name, string $path): Endpoint
    {
        return static::endpoint('PATCH', $name, $path);
    }

    /**
     * Create a new DELETE Endpoint.
     *
     * @param string $name
     * @param string $path
     * @return Endpoint
     */
    public static function delete(string $name, string $path): Endpoint
    {
        return static::endpoint('DELETE', $name, $path);
    }
    //endregion
}
