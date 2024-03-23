<?php

namespace NbsPhp\ApiWrapper\Api;

use Psr\Http\Message\ResponseInterface;
use stdClass;

class Response implements ResponseInterface
{
    use ForwardsCallsToResponseInterface;

    /**
     * Response from Guzzle.
     *
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Get response contents as string.
     *
     * @return string
     */
    public function getContents(): string
    {
        $body = $this->getBody();
        $body->rewind();

        return $body->getContents();
    }

    /**
     * Decode response JSON.
     *
     * @param bool $associative
     * @return array|stdClass|null
     */
    public function json(bool $associative = true)
    {
        return json_decode($this->getContents(), $associative);
    }
}
