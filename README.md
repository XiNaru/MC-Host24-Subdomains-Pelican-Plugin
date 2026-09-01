# MC-HOST24 Subdomains

Pelican plugin for managing Minecraft subdomains using the MC-HOST24 Public API.

## Features

* MC-HOST24 API token configuration in the plugin settings
* Support for multiple configurable Minecraft domains
* Domain selection when creating a subdomain
* Automatic creation of A/AAAA and SRV DNS records
* Automatic detection of server IP and port
* One subdomain per server
* Subdomain creation and deletion directly from the server panel
* Automatic cleanup of DNS records when a subdomain is deleted
* Graceful handling of DNS records that have already been removed
* Multi-language support
* English fallback for languages without a translation
* Integration with the default Pelican interface
* Database migration support for existing plugin installations

## API

This plugin uses the MC-HOST24 Public API.

API endpoint:

`https://mc-host24.de/api/v1`

The API is used to create and delete the required DNS records.

## Domain Configuration

Multiple Minecraft domains can be configured in the plugin settings.

Each domain requires:

* Domain ID
* Domain name

When a user creates a subdomain, the available configured domains are presented as a selectable list.

For example:

* `example.de`
* `example.net`
* `minecraft.example.com`

A user can select the desired domain when creating a subdomain.

Examples:

`survival.example.de`

`survival.example.net`

Each server can have only **one subdomain**, regardless of which configured domain is selected.

## DNS Records

When creating a subdomain, the plugin automatically creates the required DNS records through the MC-HOST24 API.

### A / AAAA Record

The plugin automatically detects whether the server allocation uses an IPv4 or IPv6 address.

* IPv4 → A record
* IPv6 → AAAA record

The plugin automatically creates an internal hostname for the server.

Example:

`mc-4.example.de`

### SRV Record

The plugin creates a Minecraft SRV record for the selected subdomain.

The SRV record automatically uses the server's current Minecraft port.

Example:

`_minecraft._tcp.survival`

The SRV target points to the automatically generated internal hostname.

This allows Minecraft users to connect using:

`survival.example.de`

without having to specify the server port manually.

## Requirements

The plugin requires:

* Pelican
* A valid MC-HOST24 API token
* At least one MC-HOST24 domain configured in the plugin settings
* A server allocation with a valid IP address and port

The user managing the server must have permission to manage the server settings.

## Installation

Install the plugin through the plugin installation method provided by Pelican and enable it in the administration panel.

After installation:

1. Open the MC-HOST24 Subdomains plugin settings.
2. Enter your MC-HOST24 API token.
3. Configure one or more Minecraft domains.
4. Save the plugin configuration.
5. Open a server's settings.
6. The MC-HOST24 Subdomains section will be available in the server settings.

The configured domains can then be selected when creating a subdomain.

## Updating the Plugin

The plugin supports database migrations for existing installations.

When updating from an older version that used a single configured domain, existing subdomain data is preserved while the plugin adds support for selecting between multiple domains.

After updating the plugin, run the normal Pelican database migrations:

`php artisan migrate`

If Pelican reports that the migrations have already been executed, no further database action is required.

## Usage

After configuration, users can create a Minecraft subdomain directly from the server settings.

The user selects:

1. The Minecraft domain.
2. The desired subdomain name.

For example:

`survival.example.de`

The plugin then:

1. Validates the subdomain.
2. Determines the server's current IP address.
3. Determines the server's current Minecraft port.
4. Creates the A or AAAA record.
5. Creates the Minecraft SRV record.
6. Stores the required information for managing the subdomain.

Each server can have only one subdomain.

## Deleting a Subdomain

Existing subdomains can be deleted directly from the server settings.

When deleting a subdomain, the plugin attempts to remove the associated DNS records and then removes the subdomain from Pelican.

If a DNS record has already been removed manually through MC-HOST24, the API may return HTTP `404`.

A `404` during deletion is treated as the DNS record already being absent. The plugin can therefore continue the deletion process and remove the corresponding local subdomain entry.

This prevents manually removed DNS records from leaving unusable subdomain entries inside Pelican.

## Permissions

Subdomain management is available when the user:

* Owns the server, or
* Has the required Pelican server settings permission.

Subdomain creation and deletion are protected server-side and cannot be performed simply by manipulating the frontend.

## Subdomain Validation

Subdomains are automatically converted to lowercase.

The following rules apply:

* Maximum length: 63 characters
* Only lowercase letters, numbers and hyphens are allowed
* The name must begin with a letter or number
* The name must end with a letter or number
* Empty subdomain names are not allowed

### Valid Examples

`survival`

`skyblock`

`server-1`

`modded-01`

### Invalid Examples

`-survival`

`survival-`

`my server`

`survival.example.de`

The domain itself must not be entered into the subdomain field.

## Language

The plugin supports multiple languages.

The translation matching the current Pelican panel language is used when available.

English is used as a fallback when no translation is available for the current panel language.

## Error Handling

The plugin validates the configuration and server information before attempting to create DNS records.

This includes:

* API token
* Configured domain
* Server allocation
* Server IP address
* Server port
* Subdomain format
* Existing subdomain assignments

If an API request fails, the plugin reports the error instead of silently creating an incomplete configuration.

If creation of the SRV record fails after the A/AAAA record has already been created, the plugin attempts to remove the previously created address record.

This helps prevent incomplete or orphaned DNS records.

## Compatibility

The plugin supports the current Pelican interface and is designed to integrate directly into the server settings.

Existing installations using the previous single-domain configuration can be updated to the multi-domain system while preserving existing subdomain data.

## Security

The MC-HOST24 API token is required for communication with the MC-HOST24 API.

Keep the API token private and do not publish it or commit it to a public repository.

All subdomain management actions are validated server-side using the user's Pelican permissions.

## License

MC-HOST24 Subdomains is released under a custom Non-Commercial License.

See the `LICENSE` file for the complete license terms.

---

# MC-HOST24 Subdomains

Pelican Plugin zur Verwaltung von Minecraft-Subdomains über die öffentliche MC-HOST24 API.

## Funktionen

* MC-HOST24 API Token in den Plugin-Einstellungen
* Unterstützung mehrerer konfigurierbarer Minecraft-Domains
* Auswahl der Domain beim Erstellen einer Subdomain
* Automatische Erstellung von A/AAAA- und SRV-DNS-Einträgen
* Automatische Erkennung von Server-IP und Port
* Eine Subdomain pro Server
* Erstellen und Löschen von Subdomains direkt in den Server-Einstellungen
* Automatische Entfernung der zugehörigen DNS-Einträge
* Saubere Behandlung bereits gelöschter DNS-Einträge
* Unterstützung mehrerer Sprachen
* Englisch als Fallback, wenn keine Übersetzung für die aktuelle Sprache vorhanden ist
* Anpassung an das Standard-Pelican-Interface
* Datenbank-Migrationen für bestehende Plugin-Installationen

## API

Das Plugin verwendet die öffentliche MC-HOST24 API.

API-Endpunkt:

`https://mc-host24.de/api/v1`

Die API wird zum Erstellen und Löschen der benötigten DNS-Einträge verwendet.

## Domain-Konfiguration

In den Plugin-Einstellungen können mehrere Minecraft-Domains konfiguriert werden.

Für jede Domain werden benötigt:

* Domain-ID
* Domainname

Beim Erstellen einer Subdomain werden alle konfigurierten Domains als Auswahl angezeigt.

Beispiel:

* `example.de`
* `example.net`
* `minecraft.example.com`

Der Benutzer kann beim Erstellen auswählen, welche Domain verwendet werden soll.

Beispiele:

`survival.example.de`

`survival.example.net`

Pro Server ist trotzdem weiterhin nur **eine Subdomain** möglich, unabhängig davon, welche konfigurierte Domain ausgewählt wurde.

## DNS-Einträge

Beim Erstellen einer Subdomain werden die benötigten DNS-Einträge automatisch über die MC-HOST24 API erstellt.

### A- / AAAA-Eintrag

Das Plugin erkennt automatisch, ob die Server-Zuteilung eine IPv4- oder IPv6-Adresse verwendet.

* IPv4 → A-Eintrag
* IPv6 → AAAA-Eintrag

Für den Server wird automatisch ein interner Hostname erstellt.

Beispiel:

`mc-4.example.de`

### SRV-Eintrag

Für die ausgewählte Subdomain wird automatisch ein Minecraft-SRV-Eintrag erstellt.

Der verwendete Port wird direkt aus der aktuellen Server-Zuteilung übernommen.

Beispiel:

`_minecraft._tcp.survival`

Das Ziel des SRV-Eintrags verweist auf den automatisch erzeugten internen Hostnamen.

Dadurch können Spieler den Server einfach über:

`survival.example.de`

erreichen, ohne den Port manuell angeben zu müssen.

## Voraussetzungen

Das Plugin benötigt:

* Pelican
* Einen gültigen MC-HOST24 API Token
* Mindestens eine konfigurierte MC-HOST24 Minecraft-Domain
* Eine Server-Zuteilung mit gültiger IP-Adresse und Port

Der Benutzer benötigt außerdem die erforderlichen Berechtigungen zur Verwaltung der Server-Einstellungen.

## Installation

Installiere das Plugin über die von Pelican bereitgestellte Plugin-Installationsmethode und aktiviere es anschließend im Administrationsbereich.

Danach:

1. Öffne die Einstellungen des MC-HOST24 Subdomains Plugins.
2. Trage deinen MC-HOST24 API Token ein.
3. Konfiguriere eine oder mehrere Minecraft-Domains.
4. Speichere die Plugin-Konfiguration.
5. Öffne die Einstellungen eines Servers.
6. Der Bereich für MC-HOST24 Subdomains wird dort angezeigt.

Beim Erstellen einer Subdomain können anschließend die konfigurierten Domains ausgewählt werden.

## Aktualisierung des Plugins

Das Plugin unterstützt Datenbank-Migrationen für bestehende Installationen.

Beim Update von älteren Versionen, die nur eine einzelne Domain unterstützt haben, bleiben bestehende Subdomain-Daten erhalten und die Unterstützung für mehrere Domains wird ergänzt.

Nach einem Plugin-Update können die normalen Pelican-Datenbank-Migrationen ausgeführt werden:

`php artisan migrate`

Wenn Pelican meldet, dass die Migrationen bereits ausgeführt wurden, ist keine weitere Datenbankaktion erforderlich.

## Verwendung

Nach der Konfiguration können Benutzer direkt in den Server-Einstellungen eine Minecraft-Subdomain erstellen.

Dabei wird ausgewählt:

1. Die Minecraft-Domain.
2. Der gewünschte Subdomain-Name.

Beispiel:

`survival.example.de`

Das Plugin:

1. Prüft den Subdomain-Namen.
2. Ermittelt die aktuelle Server-IP.
3. Ermittelt den aktuellen Minecraft-Port.
4. Erstellt den A- oder AAAA-Eintrag.
5. Erstellt den Minecraft-SRV-Eintrag.
6. Speichert die benötigten Informationen zur Verwaltung der Subdomain.

Jeder Server kann nur eine Subdomain besitzen.

## Subdomain löschen

Bestehende Subdomains können direkt in den Server-Einstellungen gelöscht werden.

Beim Löschen entfernt das Plugin die zugehörigen DNS-Einträge und anschließend den lokalen Subdomain-Eintrag in Pelican.

Wurde ein DNS-Eintrag beispielsweise bereits manuell bei MC-HOST24 gelöscht, kann die API beim Löschen mit HTTP `404` antworten.

Ein solcher `404` wird als „DNS-Eintrag ist bereits nicht mehr vorhanden“ behandelt. Das Plugin kann den Löschvorgang anschließend trotzdem abschließen.

Dadurch bleiben keine veralteten Subdomain-Einträge im Pelican-Panel zurück, wenn DNS-Einträge bereits manuell entfernt wurden.

## Berechtigungen

Die Verwaltung von Subdomains ist möglich, wenn der Benutzer:

* Besitzer des Servers ist oder
* über die erforderliche Pelican-Berechtigung für die Server-Einstellungen verfügt.

Erstellen und Löschen werden serverseitig geprüft und können nicht einfach durch Änderungen im Frontend umgangen werden.

## Validierung von Subdomains

Subdomains werden automatisch in Kleinbuchstaben umgewandelt.

Es gelten folgende Regeln:

* Maximale Länge: 63 Zeichen
* Nur Kleinbuchstaben, Zahlen und Bindestriche
* Der Name muss mit einem Buchstaben oder einer Zahl beginnen
* Der Name muss mit einem Buchstaben oder einer Zahl enden
* Leere Subdomain-Namen sind nicht erlaubt

### Gültige Beispiele

`survival`

`skyblock`

`server-1`

`modded-01`

### Ungültige Beispiele

`-survival`

`survival-`

`my server`

`survival.example.de`

In das Subdomain-Feld darf nur der eigentliche Subdomain-Name eingetragen werden. Die Domain wird separat ausgewählt.

## Sprache

Das Plugin unterstützt mehrere Sprachen.

Wenn eine Übersetzung für die aktuelle Pelican-Sprache vorhanden ist, wird diese verwendet.

Ist keine Übersetzung vorhanden, wird Englisch als Fallback verwendet.

## Fehlerbehandlung

Das Plugin prüft die Konfiguration und Serverinformationen vor dem Erstellen der DNS-Einträge.

Geprüft werden unter anderem:

* API-Token
* Konfigurierte Domain
* Server-Zuteilung
* Server-IP-Adresse
* Server-Port
* Subdomain-Format
* Bereits vorhandene Subdomain

Schlägt eine API-Anfrage fehl, wird der Fehler angezeigt, anstatt eine unvollständige Konfiguration stillschweigend als erfolgreich zu behandeln.

Wenn beim Erstellen des SRV-Eintrags ein Fehler auftritt, nachdem der A-/AAAA-Eintrag bereits erstellt wurde, versucht das Plugin, den zuvor erstellten Address-Record wieder zu entfernen.

Dadurch sollen unvollständige oder verwaiste DNS-Einträge vermieden werden.

## Kompatibilität

Das Plugin ist für die aktuelle Pelican-Oberfläche ausgelegt und integriert sich direkt in die Server-Einstellungen.

Bestehende Installationen mit der bisherigen Einzel-Domain-Konfiguration können auf das Mehrfach-Domain-System aktualisiert werden, ohne vorhandene Subdomain-Daten zu verlieren.

## Sicherheit

Für die Kommunikation mit der MC-HOST24 API wird ein MC-HOST24 API Token benötigt.

Der API Token sollte geheim gehalten und niemals veröffentlicht oder in ein öffentliches Repository hochgeladen werden.

Alle Aktionen zur Verwaltung von Subdomains werden serverseitig anhand der Pelican-Berechtigungen geprüft.

## Lizenz

MC-HOST24 Subdomains steht unter einer eigenen Non-Commercial License.

Die vollständigen Lizenzbedingungen stehen in der Datei `LICENSE`.
