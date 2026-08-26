<?php

return [
    'api_token' => env('MCHOST24_SUBDOMAINS_API_TOKEN', ''),

    'domain_id' => env('MCHOST24_SUBDOMAINS_DOMAIN_ID', ''),

    'domain' => env('MCHOST24_SUBDOMAINS_DOMAIN', ''),

    'subdomain_limit' => (int) env('MCHOST24_SUBDOMAINS_LIMIT', 1),
];