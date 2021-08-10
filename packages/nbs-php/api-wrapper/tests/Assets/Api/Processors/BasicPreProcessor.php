<?php

namespace Tests\Assets\Api\Processors;

use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;

class BasicPreProcessor extends Processor
{
    public static $called = false;

    public static function handle(Request $request, callable $next): Response
    {
        static::$called = true;

        return $next($request);
    }
}
