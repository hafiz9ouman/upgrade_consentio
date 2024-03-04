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
        '192.168.1.1',
        '192.168.1.2',
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

    'headers' => Request::HEADER_X_FORWARDED_FOR,

];
