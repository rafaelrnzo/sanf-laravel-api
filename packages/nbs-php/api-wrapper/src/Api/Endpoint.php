<?php

namespace NbsPhp\ApiWrapper\Api;

class Endpoint
{
    //region Data
    /**
     * The Endpoint HTTP method.
     *
     * @var string
     */
    protected $method;

    /**
     * The Endpoint base URL.
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * The Endpoint URL path.
     *
     * @var string
     */
    protected $path;

    /**
     * Registered Processors for the Endpoint.
     *
     * @var array
     */
    protected $processors = [];
    //endregion

    //region Builders
    /**
     * Endpoint constructor.
     *
     * @param string $method
     * @param string $path
     */
    public function __construct(string $method, string $path)
    {
        $this->method = $method;
        $this->path = ltrim($path, '/');
    }

    /**
     * Set the Endpoint base URL.
     *
     * @param string $baseUrl
     * @return $this
     */
    public function baseUrl(string $baseUrl): Endpoint
    {
        $this->baseUrl = rtrim($baseUrl, '/');

        return $this;
    }

    /**
     * Add new Processors to the endpoint.
     *
     * @param array|string $processors
     * @return $this
     */
    public function processor($processors): Endpoint
    {
        $this->processors = array_unique(array_merge($this->processors, (array) $processors));

        return $this;
    }
    //endregion

    //region Getters
    /**
     * Get the Endpoint HTTP method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get the Endpoint URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return ltrim("{$this->baseUrl}/{$this->path}", '/');
    }

    /**
     * Get all of the registered Processors for the Endpoint.
     *
     * @return array
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }
    //endregion
}
