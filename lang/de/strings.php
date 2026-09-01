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

    'minecraft_domain' => 'Minecraft-Domains',
    'domain_placeholder' => 'Zuerst einen gültigen API-Token eingeben',
    'domain_help' => 'Wähle eine oder mehrere Domains aus, die Benutzern für Minecraft-Subdomains zur Verfügung stehen.',
    'domain_required' => 'Bitte wähle eine Domain aus.',
    'domain_not_configured' => 'Für dieses Plugin ist keine gültige Minecraft-Domain konfiguriert.',
    'domain_not_selected' => 'Es wurde keine Minecraft-Domain ausgewählt.',
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
    'subdomain_already_used' => 'Diese Subdomain wird bereits verwendet.',
    'subdomain_required' => 'Bitte gib eine Subdomain ein.',
    'subdomain_too_long' => 'Die Subdomain darf maximal 63 Zeichen lang sein.',
    'subdomain_invalid' => 'Die Subdomain enthält ungültige Zeichen.',
    'allocation_not_found' => 'Für diesen Server konnte keine Allocation gefunden werden.',
    'server_ip_not_found' => 'Die Server-IP konnte nicht ermittelt werden.',
    'invalid_server_port' => 'Der Server-Port ist ungültig.',
    'api_token_not_configured' => 'Es wurde kein MC-HOST24 API-Token konfiguriert.',
    'address_record_failed' => 'Der A/AAAA-DNS-Eintrag konnte nicht erstellt werden.',
    'address_record_id_missing' => 'Die MC-HOST24 API hat keine ID für den A/AAAA-DNS-Eintrag zurückgegeben.',
    'srv_record_failed' => 'Der SRV-DNS-Eintrag konnte nicht erstellt werden.',
    'srv_record_id_missing' => 'Die MC-HOST24 API hat keine ID für den SRV-DNS-Eintrag zurückgegeben.',
    'dns_record_delete_failed' => 'Der DNS-Eintrag konnte nicht gelöscht werden.',

    'created_title' => 'Subdomain erstellt',
    'created_body' => 'Die Minecraft-Subdomain wurde erfolgreich bei MC-HOST24 erstellt.',
    'create_failed_title' => 'Subdomain konnte nicht erstellt werden',
    'deleted_title' => 'Subdomain gelöscht',
    'delete_failed_title' => 'Subdomain konnte nicht gelöscht werden',
];