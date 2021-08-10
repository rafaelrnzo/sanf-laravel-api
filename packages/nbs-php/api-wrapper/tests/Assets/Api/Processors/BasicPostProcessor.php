<?php

namespace Tests\Assets\Api\Processors;

use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;

class BasicPostProcessor extends Processor
{
    public static $called = false;

    public static function handle(Request $request, callable $next): Response
    {
        $response = $next($request);

        static::$called = true;

        return $response;
    }
}
