# Changelog

## 1.1.0

### Änderungen

* Das Plugin verwendet jetzt Pelicans automatisches Übersetzungssystem.
* Unterstützung für alle verfügbaren Panel-Sprachen hinzugefügt.
* Englisch wird als Fallback verwendet, wenn für die aktuelle Panel-Sprache keine Übersetzung vorhanden ist.
* Das konfigurierbare Subdomain-Limit wurde aus den Plugin-Einstellungen entfernt.
* Jeder Server kann jetzt nur noch eine Subdomain besitzen.
* Die Plugin-Einstellungen wurden um die nicht mehr benötigte Subdomain-Limit-Konfiguration bereinigt.
* Das README wurde an das neue Subdomain- und Sprachverhalten angepasst.
* Die unnötige manuelle Registrierung der Übersetzungen wurde entfernt.
* Die Plugin-Struktur wurde stärker an die offizielle Pelican-Plugin-Dokumentation angepasst.

### Behoben

* Die Übersetzungsbehandlung in den Plugin-Einstellungen und Server-Einstellungen wurde korrigiert.
* Die automatische Erstellung und Löschung von A/AAAA- und SRV-DNS-Einträgen bleibt erhalten.
* Die automatische Erkennung von Server-IP und Minecraft-Port bleibt erhalten.

## 1.0.0

* Erstveröffentlichung.
