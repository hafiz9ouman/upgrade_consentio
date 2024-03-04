<?php

use Illuminate\Http\Request;

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Set an array of trusted proxies that should be used to determine the
    | client IP addresses. Typically, this will be the proxies that are
    | provided by your hosting environment or other services.
    |
    */

    'proxies' => [
        // Example IP addresses
        '192.168.1.10',
    ],

    /*
    |--------------------------------------------------------------------------
    | Trusted Headers
    |--------------------------------------------------------------------------
    |
    | Optionally, you may set the headers that should be used to detect proxies
    | in situations where the proxy does not related to the remote IP. These
    | values are used only if your proxies are setting these headers.
    |
    | Supported values: Request::HEADER_X_FORWARDED_FOR or Request::HEADER_CLIENT_IP
    |
    */

    'headers' => [
        (defined('Request::HEADER_FORWARDED') ? Request::HEADER_FORWARDED : 'forwarded') => 'FORWARDED',
        Request::HEADER_X_FORWARDED_FOR    => 'X_FORWARDED_FOR',
        Request::HEADER_X_FORWARDED_HOST   => 'X_FORWARDED_HOST',
        Request::HEADER_X_FORWARDED_PROTO  => 'X_FORWARDED_PROTO',
        Request::HEADER_X_FORWARDED_PORT  => 'X_FORWARDED_PORT',
    ]

];
