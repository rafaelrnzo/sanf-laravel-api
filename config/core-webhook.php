<?php

return [
    'secret' => env('CORE_WEBHOOK_SECRET'),
    'signature_header' => env('CORE_WEBHOOK_SIGNATURE_HEADER', 'X-Signature'),
    'timestamp_header' => env('CORE_WEBHOOK_TIMESTAMP_HEADER', 'X-Timestamp'),
    'signature_tolerance' => (int) env('CORE_WEBHOOK_SIGNATURE_TOLERANCE', 0),
];
