<?php

/**
 * Additional SANF config.
 */

return [
    'company_name' => 'PT Surya Artha Nusantara', // Used for display in Dashboard and Email,
    'company_initials' => 'SANF',
    'company' => [ // Used in Documents and allocation
        'company_prefix' => env('COMPANY_PREFIX', 'PT'),
        'company_name' => env('COMPANY_NAME', 'Surya Artha Nusantara Finance'),
        'company_initials' => env('COMPANY_INITIALS', 'SANF'),
        'address_line_1' => env('COMPANY_ADDRESS_LINE_1', '18 Office Park lt 23'),
        'address_line_2' => env('COMPANY_ADDRESS_LINE_2', 'Jl. TB Simatupang Kav 18'),
        'address_line_3' => env('COMPANY_ADDRESS_LINE_3', 'Jakarta Selatan'),
        'bank_provider' => env('COMPANY_BANK_PROVIDER', 'MANDIRI'), // Bank Name
        'bank_account_no' => env('COMPANY_BANK_ACCOUNT_NO', '1270004589980'), // Bank Account Number
        'bank_owner' => env('COMPANY_BANK_OWNER', 'PT Surya Artha Nusantara Finance'), // Bank On Behalf Of
        'bank_id' => env('COMPANY_BANK_ID', '0'), // Bank ID in SANF Core
    ],
];
