# Changelog

## 1.2.0

### Added

* Added support for multiple configurable Minecraft domains.
* Added domain selection when creating a subdomain.
* Added database migration support for the new multi-domain configuration.
* Added backwards compatibility with the previous single-domain configuration.

### Changes

* Updated subdomain creation to use the selected Minecraft domain.
* Updated DNS record handling to store the associated MC-HOST24 domain ID.
* Updated the plugin to automatically determine whether an A or AAAA record is required based on the server IP address.
* Improved the server settings interface integration.
* Updated the README to document the new multi-domain functionality.
* Updated the plugin metadata to version 1.2.0.

## 1.1.0

### Changes

* The plugin now uses Pelican's automatic translation system.
* Added support for all available panel languages.
* English is now used as the fallback when no translation is available for the current panel language.
* Removed the configurable subdomain limit from the plugin settings.
* Each server can now have only one subdomain.
* Updated the plugin settings to remove the no longer required subdomain limit configuration.
* Updated the README to reflect the new subdomain and language behavior.
* Removed unnecessary manual translation registration.
* Updated the plugin structure to better follow the official Pelican plugin documentation.

### Fixed

* Fixed translation handling in the plugin settings and server settings.
* Preserved automatic creation and deletion of A/AAAA and SRV DNS records.
* Preserved automatic detection of the server IP and Minecraft port.

## 1.0.0

* Initial release.
