<?php

/** @var \Illuminate\Config\Repository $config */
$config = app('config');

$routeConfig = [
    'namespace' => $config->get('fcm.namespace'),
    'prefix' => $config->get('fcm.route_prefix'),
    'domain' => $config->get('fcm.route_domain'),
    'middleware' => $config->get('fcm.middleware'),
];

app('router')->group($routeConfig, function ($router) {
    $router->post('token', [
        'uses' => 'FcmController@saveToken',
        'as' => 'fcm.saveToken',
    ]);
    $router->post('subscribe/topic', [
        'uses' => 'FcmController@subscribeTopic',
        'as' => 'fcm.subscribeTopic',
    ]);
    $router->post('unsubscribe/topic', [
        'uses' => 'FcmController@unsubscribeTopic',
        'as' => 'fcm.unsubscribeTopic',
    ]);
});
