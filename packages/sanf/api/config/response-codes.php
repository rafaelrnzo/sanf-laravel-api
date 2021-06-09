<?php

return [
    \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class => [
        'status' => 405,
        'code' => '405',
        'message' => __('Method not allowed'),
    ],
    \Illuminate\Validation\ValidationException::class => [
        'status' => 422,
        'code' => '422',
        'message' => __('The given data was invalid'),
    ],
    \Illuminate\Database\Eloquent\ModelNotFoundException::class => [
        'status' => 404,
        'code' => 404,
        'message' => __('Data Not Found'),
    ],
    \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => [
        'status' => 404,
        'code' => 404,
        'message' => __('Route Not Found'),
    ],
    'AUTH001' => [
        'status' => 400,
        'message' => __('Invalid Credentials')
    ],
    'AUTH002' => [
        'status' => 400,
        'message' => __('Password Reset Fail')
    ],
    'AUTH003' => [
        'status' => 401,
        'message' => __('Unauthorized')
    ],
];
