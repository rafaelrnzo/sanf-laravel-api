<?php

namespace NbsPhp\ApiWrapper\Api;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\StreamInterface;

class Request
{
    //region Data
    /**
     * Guzzle Client.
     *
     * @var GuzzleClient
     */
    protected $client;

    /**
     * The Request Endpoint.
     *
     * @var Endpoint
     */
    protected $endpoint;

    /**
     * URL path parameters.
     *
     * @var array
     */
    protected $pathParams = [];

    /**
     * Request options.
     *
     * @var array
     */
    protected $options = [];
    //endregion

    //region Builders
    /**
     * Create a new Request for the specified Endpoint.
     *
     * @param string $name
     * @param GuzzleClient|null $client
     * @return static
     * @throws Exceptions\EndpointNotDefinedException
     */
    public static function route(string $name, GuzzleClient $client = null): Request
    {
        return new static(Route::find($name), $client);
    }

    /**
     * Request constructor.
     *
     * @param Endpoint $endpoint
     * @param GuzzleClient|null $client
     */
    public function __construct(Endpoint $endpoint, ?GuzzleClient $client = null)
    {
        $this->endpoint = $endpoint;
        $this->client = $client ?? new GuzzleClient();
    }

    /**
     * Add Path parameter values.
     * Path parameters keys may be encoded in the Endpoint URL as '{key}', and will
     * be replaced with the appropriate value using the key/value pairs given here.
     *
     * @param array $pathParams
     * @return $this
     */
    public function pathParams(array $pathParams): Request
    {
        $this->pathParams = array_merge($this->pathParams, $pathParams);

        return $this;
    }

    /**
     * Set auth parameters.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html#auth
     * @param array|string $auth
     * @return $this
     */
    public function auth($auth): Request
    {
        $this->options['auth'] = $auth;

        return $this;
    }

    /**
     * Add request headers.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html#headers
     * @param array $headers
     * @return $this
     */
    public function headers(array $headers): Request
    {
        $this->options['headers'] = array_merge($this->options['headers'] ?? [], $headers);

        return $this;
    }

    /**
     * Add URL-encoded Query parameter values.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html#query
     * @param array $queryParams
     * @return $this
     */
    public function queryParams(array $queryParams): Request
    {
        $this->options['query'] = array_merge($this->options['query'] ?? [], $queryParams);

        return $this;
    }

    /**
     * Set body value.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html#body
     * @param string|resource|StreamInterface $body
     * @return $this
     */
    public function body($body): Request
    {
        $this->options['body'] = $body;

        return $this;
    }

    /**
     * Add request JSON data.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html#json
     * @param array $data
     * @return $this
     */
    public function json(array $data): Request
    {
        $this->options['json'] = array_merge($this->options['json'] ?? [], $data);

        return $this;
    }

    /**
     * Add request form data.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html#form-params
     * @param array $data
     * @return $this
     */
    public function formParams(array $data): Request
    {
        $this->options['form_params'] = array_merge($this->options['form_params'] ?? [], $data);

        return $this;
    }

    /**
     * Add request multipart form data.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html#multipart
     * @param array $data
     * @return $this
     */
    public function multipart(array $data): Request
    {
        $this->options['multipart'] = array_merge($this->options['multipart'] ?? [], $data);

        return $this;
    }

    /**
     * Set additional Request options.
     *
     * @see https://docs.guzzlephp.org/en/stable/request-options.html
     * @param array $options
     * @return $this
     */
    public function options(array $options): Request
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }
    //endregion

    //region Getters
    /**
     * Get Request method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->endpoint->getMethod();
    }

    /**
     * Get the full Request URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        $url = $this->endpoint->getUrl();

        foreach ($this->pathParams as $key => $value)
        {
            $url = str_replace("{{$key}}", $value, $url);
        }

        return $url;
    }

    /**
     * Get Request options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
    //endregion

    //region Send
    /**
     * Send the API Request.
     *
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(): Response
    {
        $callback = function (Request $request): Response {
            return new Response($this->client->request(
                $request->getMethod(),
                $request->getUrl(),
                $request->getOptions()
            ));
        };

        foreach ($this->endpoint->getProcessors() as $processor)
        {
            $callback = function (Request $request) use ($callback, $processor): Response {
                return call_user_func_array("$processor::handle", [$request, $callback]);
            };
        }

        return $callback($this);
    }
    //endregion
}
