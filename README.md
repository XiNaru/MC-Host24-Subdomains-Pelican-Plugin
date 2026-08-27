# MC-HOST24 Subdomains Pelican Plugin

Pelican plugin for managing Minecraft subdomains using the MC-HOST24 API.

## Features

* MC-HOST24 API token configuration in the plugin settings
* Minecraft domain selection
* Automatic creation of A/AAAA and SRV DNS records
* Automatic detection of server IP and port
* One subdomain per server
* Subdomain creation and deletion directly from the server panel
* Multi-language support
* English fallback for languages without a translation
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

Each server can have only one subdomain.

For example:

`survival.example.de`

The plugin automatically creates the required DNS records and configures the SRV record to use the server's current Minecraft port.

## Language

The plugin supports multiple languages.

The language matching the current Pelican panel language is used when available.

English is used as a fallback when no translation is available for the current panel language.

### Translation Contributions

If your language is not currently supported, you are welcome to send me a translation.

After review, I can officially include the translation in the plugin.

## License

MC-HOST24 Subdomains is released under a custom Non-Commercial License.

See the `LICENSE` file for the complete license terms.

---

# MC-HOST24 Subdomains

Pelican Plugin zur Verwaltung von Minecraft-Subdomains über die MC-HOST24 API.

## Funktionen

* MC-HOST24 API Token in den Plugin-Einstellungen
* Auswahl der Minecraft-Domain
* Automatische Erstellung von A/AAAA- und SRV-DNS-Einträgen
* Automatische Erkennung von Server-IP und Port
* Eine Subdomain pro Server
* Erstellen und Löschen von Subdomains direkt in den Server-Einstellungen
* Unterstützung mehrerer Sprachen
* Englisch als Fallback, wenn keine Übersetzung für die aktuelle Sprache vorhanden ist
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

Jeder Server kann nur eine Subdomain besitzen.

Beispiel:

`survival.example.de`

Das Plugin erstellt automatisch die benötigten DNS-Einträge und konfiguriert den SRV-Eintrag mit dem aktuellen Minecraft-Port des Servers.

## Sprache

Das Plugin unterstützt mehrere Sprachen.

Wenn eine Übersetzung für die aktuelle Pelican-Sprache vorhanden ist, wird diese verwendet.

Ist keine Übersetzung für die aktuelle Sprache vorhanden, wird Englisch als Fallback verwendet.

### Übersetzungsbeiträge

Wenn deine Sprache derzeit nicht unterstützt wird, kannst du mir gerne eine Übersetzung schicken.

Nach einer Prüfung kann ich die Übersetzung offiziell in das Plugin aufnehmen.

## Lizenz

MC-HOST24 Subdomains steht unter einer eigenen Non-Commercial License.

Die vollständigen Lizenzbedingungen stehen in der Datei `LICENSE`.