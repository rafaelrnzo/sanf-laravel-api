<?php

return [
    'secret' => env('JWT_SECRET'),
    'ttl' => env('JWT_TTL'),
    'refresh_ttl' => env('JWT_REFRESH_TTL'),
    'issuer' => env('JWT_ISSUER'),
    'id_field' => env('JWT_ID_FIELD'),
    'include' => env('JWT_INCLUDE'),
];
