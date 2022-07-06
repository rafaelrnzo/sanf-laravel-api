<?php


namespace Sanf\Integration;


use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use stdClass;

class TekenAjaInternalApiClient {
    const DEFAULT_SKIP = 0;
    const DEFAULT_LIMIT = 2147483647;
    const DEFAULT_ORDER = 'Latest';

    protected $client;

    public function __construct()
    {
        //TODO INJECT
        $this->client = app(\GuzzleHttp\Client::class);
    }

    /**
     * {
        "11": "ACEH",
        "51": "BALI",
        "36": "BANTEN",
     */
    public function getProvinces()
    {
        $response = Request::route('location.province', $this->client)->send();
        return $response->json();
    }
}
