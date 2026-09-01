<?php

return [
    'api_token' => env('MCHOST24_SUBDOMAINS_API_TOKEN', ''),

    /*
     * Neue Mehrfach-Domain-Konfiguration.
     *
     * Format:
     *
     * [
     *     [
     *         'id' => 123,
     *         'domain' => 'example.de',
     *     ],
     * ]
     */
    'domains' => (static function (): array {
        $json = (string) env(
            'MCHOST24_SUBDOMAINS_DOMAINS',
            '[]'
        );

        $domains = json_decode($json, true);

        return is_array($domains) ? $domains : [];
    })(),

    /*
     * Alte Konfiguration bleibt für Rückwärtskompatibilität erhalten.
     */
    'domain_id' => env('MCHOST24_SUBDOMAINS_DOMAIN_ID', ''),

    'domain' => env('MCHOST24_SUBDOMAINS_DOMAIN', ''),
];