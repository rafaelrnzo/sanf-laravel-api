<?php

namespace NbsPhp\ApiWrapper\Api;

abstract class Processor
{
    abstract public static function handle(Request $request, callable $next): Response;
}
