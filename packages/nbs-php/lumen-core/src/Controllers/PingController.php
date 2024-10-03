<?php

namespace NbsPhp\Core\Controllers;

use PragmaRX\Health\Service;

class PingController extends RestApiController
{
    /**
     * @var Service
     */
    private $healthService;

    /**
     * Health constructor.
     *
     * @param Service $healthService
     */
    public function __construct(Service $healthService)
    {
        parent::__construct();
        $this->healthService = $healthService;
    }

    public function getPing()
    {
        $this->healthService->setAction('resource');
        try {
            /** @var resource $uptimeCheker */
            $uptimeCheker = $this->healthService->resource('serveruptime');
            $uptime = $uptimeCheker->checker->check()->errorMessage;
        } catch (\Exception $exception) {
            $uptime = null;
        }
        /** @var resource $dbChecker */
        $dbChecker = $this->healthService->resource('database');
        if (!$dbChecker->isHealthy()) {
            throw new \Exception('Could not query to DB');
        }
        $response = [
            'version' => '',
            'build_hash' => '',
            'timestamp' => '',
            'server_uptime' => $uptime,
        ];

        return $this->responseOk('OK', array_filter($response));
    }
}
