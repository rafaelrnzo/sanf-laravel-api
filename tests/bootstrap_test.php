<?php

require_once __DIR__ . '/../vendor/autoload.php';

static $app;

if ($app) {
    return $app;
}

$app = require __DIR__ . '/../bootstrap/app.php';
$app->boot();

\Illuminate\Support\Facades\Facade::setFacadeApplication($app);

return $app;
