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
    'minecraft_domain' => 'Minecraft Domain',
    'domain_placeholder' => 'Enter a valid API token first',
    'domain_help' => 'Available domains are loaded automatically using your MC-HOST24 API token. The internal domain ID is handled automatically and does not need to be entered manually.',
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
    'created_title' => 'Subdomain created',
    'created_body' => 'The Minecraft subdomain was successfully created at MC-HOST24.',
    'create_failed_title' => 'Subdomain could not be created',
    'deleted_title' => 'Subdomain deleted',
    'delete_failed_title' => 'Subdomain could not be deleted',
];