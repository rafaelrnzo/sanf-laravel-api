<?php

namespace NbsPhp\Core\Jobs;

use NbsPhp\Core\AbstractJob;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sanf\Core\Modules\HttpLog\Repositories\ApiRequestRepositoryInterface;

class LogApiRequestToDatabaseJob extends AbstractJob
{
    protected RequestInterface $request;
    protected $requestBody;
    protected ResponseInterface $response;
    protected $responseBody;
    protected $userId;

    /**
     * LogApiRequestToDatabaseJob constructor.
     * @param RequestInterface $request
     * @param $requestBody
     * @param ResponseInterface $response
     * @param $responseBody
     */
    public function __construct(RequestInterface $request, $requestBody, ResponseInterface $response, $responseBody, $userId = null)
    {
        $this->request = $request;
        $this->requestBody = $requestBody;
        $this->response = $response;
        $this->responseBody = $responseBody;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ApiRequestRepositoryInterface $repository)
    {
        $censoredKeys = config('guzzle-logger.censor.bad-keys');
        $repository->create([
            'user_id' => $this->userId,
            'request_id' => optional($this->request->getHeader('X-Request-ID'))[0],
            'status_code' => $this->response->getStatusCode(),
            'host' => ($this->request->getUri()->getPort()) ? "{$this->request->getUri()->getHost()}:{$this->request->getUri()->getPort()}" : $this->request->getUri()->getHost(),
            'method' => $this->request->getMethod(),
            'path' => $this->request->getUri()->getPath(),
            'header' => $this->censoringNestedArray($censoredKeys, $this->request->getHeaders()),
            'query' => $this->censoringNestedArray($censoredKeys, $this->request->getUri()->getQuery()),
            'body' => $this->censoringNestedArray($censoredKeys, $this->requestBody, ),
            'response' => $this->censoringNestedArray($censoredKeys, $this->responseBody),
        ]);
    }

    protected function censoringNestedArray($needles, $haystack)
    {
        if (!is_array($haystack)) {
            return $haystack;
        }
        $censor = config('guzzle-logger.censor.replacement');
        $flattenArray = array_dot($haystack);
        foreach ($needles as $needle) {
            foreach ($flattenArray as $key => $value) {
                $caseInsensitiveKey = strtolower($key);
                if (in_array($needle, explode('.', $caseInsensitiveKey))) {
                    array_set($haystack, $key, $censor);
                }
            }
        }

        return $haystack;
    }
}
