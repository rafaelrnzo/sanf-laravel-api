<?php

namespace NbsPhp\ApiWrapper;

class ApiWrapper
{
    public static function load(string $file)
    {
        require_once $file;
    }
}
