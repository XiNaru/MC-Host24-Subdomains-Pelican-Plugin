<?php

return [
    'api_token' => 'MC-HOST24 API Token',
    'api_token_help' => 'Trage hier deinen MC-HOST24 API-Token ein.',
    'api_token_create' => 'API-Token erstellen',
    'api_token_command' => <<<'TEXT'
curl -X POST "https://mc-host24.de/api/v1/token" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "DEIN_BENUTZERNAME",
    "password": "DEIN_PASSWORT",
    "tfa": 123456
  }'
TEXT,
    'tfa_title' => 'Hinweis zu tfa',
    'tfa_help' => 'Das Feld "tfa" ist optional und wird nur benötigt, wenn für dein MC-HOST24-Konto die Zwei-Faktor-Authentifizierung (2FA) aktiviert ist. In diesem Fall wird der aktuelle 6-stellige 2FA-Code eingetragen.',
    'minecraft_domain' => 'Minecraft-Domain',
    'domain_placeholder' => 'Zuerst einen gültigen API-Token eingeben',
    'domain_help' => 'Die verfügbaren Domains werden automatisch anhand deines MC-HOST24 API-Tokens geladen. Die interne Domain-ID wird automatisch verwendet und muss nicht manuell eingetragen werden.',
    'settings_saved' => 'Einstellungen gespeichert',

    'title' => 'Subdomain',
    'description' => 'Erstelle eine eigene Minecraft-Adresse für diesen Server.',
    'field' => 'Subdomain',
    'helper' => 'Nur Kleinbuchstaben, Zahlen und Bindestriche.',
    'minecraft_address' => 'Minecraft-Adresse',
    'port' => 'Port',
    'create' => 'Subdomain erstellen',
    'delete' => 'Subdomain löschen',
    'delete_confirmation' => 'Möchtest du diese Subdomain wirklich löschen?',
    'already_exists' => 'Für diesen Server existiert bereits eine Subdomain.',
    'created_title' => 'Subdomain erstellt',
    'created_body' => 'Die Minecraft-Subdomain wurde erfolgreich bei MC-HOST24 erstellt.',
    'create_failed_title' => 'Subdomain konnte nicht erstellt werden',
    'deleted_title' => 'Subdomain gelöscht',
    'delete_failed_title' => 'Subdomain konnte nicht gelöscht werden',
];