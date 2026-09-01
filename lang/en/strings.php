<?php

return [
    'api_token' => 'MC-HOST24 API Token',
    'api_token_help' => 'Enter your MC-HOST24 API token here.',
    'api_token_create' => 'Create API token',
    'api_token_command' => <<<'TEXT'
curl -X POST "https://mc-host24.de/api/v1/token" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "YOUR_USERNAME",
    "password": "YOUR_PASSWORD",
    "tfa": 123456
  }'
TEXT,
    'tfa_title' => 'tfa notice',
    'tfa_help' => 'The "tfa" field is optional and is only required if two-factor authentication (2FA) is enabled for your MC-HOST24 account. In that case, enter the current 6-digit 2FA code.',

    'minecraft_domain' => 'Minecraft Domains',
    'domain_placeholder' => 'Enter a valid API token first',
    'domain_help' => 'Select one or more domains that should be available to users for Minecraft subdomains.',
    'domain_required' => 'Please select a domain.',
    'domain_not_configured' => 'No valid Minecraft domain is configured for this plugin.',
    'domain_not_selected' => 'No Minecraft domain was selected.',
    'settings_saved' => 'Settings saved',

    'title' => 'Subdomain',
    'description' => 'Create a custom Minecraft address for this server.',
    'field' => 'Subdomain',
    'helper' => 'Only lowercase letters, numbers and hyphens.',
    'minecraft_address' => 'Minecraft Address',
    'port' => 'Port',
    'create' => 'Create subdomain',
    'delete' => 'Delete subdomain',
    'delete_confirmation' => 'Do you really want to delete this subdomain?',
    'already_exists' => 'A subdomain already exists for this server.',
    'subdomain_already_used' => 'This subdomain is already in use.',
    'subdomain_required' => 'Please enter a subdomain.',
    'subdomain_too_long' => 'The subdomain may not be longer than 63 characters.',
    'subdomain_invalid' => 'The subdomain contains invalid characters.',
    'allocation_not_found' => 'No allocation could be found for this server.',
    'server_ip_not_found' => 'The server IP could not be determined.',
    'invalid_server_port' => 'The server port is invalid.',
    'api_token_not_configured' => 'No MC-HOST24 API token has been configured.',
    'address_record_failed' => 'The A/AAAA DNS record could not be created.',
    'address_record_id_missing' => 'The MC-HOST24 API did not return an ID for the A/AAAA DNS record.',
    'srv_record_failed' => 'The SRV DNS record could not be created.',
    'srv_record_id_missing' => 'The MC-HOST24 API did not return an ID for the SRV DNS record.',
    'dns_record_delete_failed' => 'The DNS record could not be deleted.',

    'created_title' => 'Subdomain created',
    'created_body' => 'The Minecraft subdomain was successfully created at MC-HOST24.',
    'create_failed_title' => 'Subdomain could not be created',
    'deleted_title' => 'Subdomain deleted',
    'delete_failed_title' => 'Subdomain could not be deleted',
];