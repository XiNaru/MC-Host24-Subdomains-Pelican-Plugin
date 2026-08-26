# MC-HOST24 Subdomains

Pelican plugin for managing Minecraft subdomains using the MC-HOST24 API.

## Features

* MC-HOST24 API token configuration in the plugin settings
* Minecraft domain selection
* Subdomain limit configuration
* Automatic creation of A/AAAA and SRV DNS records
* Automatic detection of server IP and port
* Subdomain creation and deletion directly from the server panel
* German and English language support
* English fallback for all languages except German
* Integration with the default Pelican interface

## API

This plugin uses the MC-HOST24 Public API.

API:

https://mc-host24.de/api/v1

## Installation

Install the plugin through your Pelican plugin installation method and enable it in the administration panel.

After installation, open the plugin settings and enter your MC-HOST24 API token.

The available Minecraft domains are loaded automatically from the MC-HOST24 API.

## Usage

After configuration, users can create a Minecraft subdomain directly from the server settings.

For example:

`survival.example.de`

The plugin automatically creates the required DNS records and configures the SRV record to use the server's current Minecraft port.

## Language

The plugin uses English by default.

* German panel language (`de`) → German
* Any other panel language → English

## License

MC-HOST24 Subdomains is released under a custom Non-Commercial License.

See the `LICENSE` file for the complete license terms.

---

# MC-HOST24 Subdomains

Pelican Plugin zur Verwaltung von Minecraft-Subdomains über die MC-HOST24 API.

## Funktionen

* MC-HOST24 API Token in den Plugin-Einstellungen
* Auswahl der Minecraft-Domain
* Konfiguration des Subdomain-Limits
* Automatische Erstellung von A/AAAA- und SRV-DNS-Einträgen
* Automatische Erkennung von Server-IP und Port
* Erstellen und Löschen von Subdomains direkt in den Server-Einstellungen
* Unterstützung für Deutsch und Englisch
* Englisch als Fallback für alle Sprachen außer Deutsch
* Anpassung an das Standard-Pelican-Interface

## API

Das Plugin verwendet die MC-HOST24 Public API.

API:

https://mc-host24.de/api/v1

## Installation

Installiere das Plugin über die von Pelican bereitgestellte Plugin-Installationsmethode und aktiviere es anschließend im Administrationsbereich.

Öffne danach die Plugin-Einstellungen und trage deinen MC-HOST24 API Token ein.

Die verfügbaren Minecraft-Domains werden automatisch über die MC-HOST24 API geladen.

## Verwendung

Nach der Konfiguration können Benutzer direkt in den Server-Einstellungen eine Minecraft-Subdomain erstellen.

Beispiel:

`survival.example.de`

Das Plugin erstellt automatisch die benötigten DNS-Einträge und konfiguriert den SRV-Eintrag mit dem aktuellen Minecraft-Port des Servers.

## Sprache

Das Plugin verwendet standardmäßig Englisch.

* Deutsche Panel-Sprache (`de`) → Deutsch
* Jede andere Panel-Sprache → Englisch

## Lizenz

MC-HOST24 Subdomains steht unter einer eigenen Non-Commercial License.

Die vollständigen Lizenzbedingungen stehen in der Datei `LICENSE`.
