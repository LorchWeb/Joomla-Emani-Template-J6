# Emani: Migration zu Joomla 6

## Ziel

Das Joomla51-Template Emani soll nativ unter Joomla 6 funktionieren, ohne dass das Plugin **Behaviour – Backward Compatibility** aktiviert sein muss.

Das verbindliche Releaseprodukt ist ein eigenständig distributierbares Template-ZIP. Es muss auf beliebigen Kundenservern als **Update eines bereits installierten Joomla-5-Templates `j51_emani`** eingespielt werden können. Nach dem Update muss dasselbe Template unter Joomla 6 ohne Compatibility-Plugin weiterlaufen.

Der Zielstand soll:

- unter Joomla 6.0 sowie der aktuellen Joomla-6-Version funktionieren;
- PHP 8.3 und nach Möglichkeit PHP 8.4 unterstützen;
- ohne entfernte CMS-Klassen, Legacy-Konstanten und Compat-Helper auskommen;
- Frontend, Template-Konfiguration, Offline- und Fehlerseite unterstützen;
- vorhandene Content-, Menü-, Modul- und Slideshow-Overrides funktional erhalten;
- ein sauberes und reproduzierbares Installations-ZIP ergeben;
- vorhandene Template-Stile, Stilzuordnungen und Parameter beim Update unverändert erhalten;
- kundenseitige Anpassungen wie `custom.css` während des Upgrades kontrolliert bewahren;
- sowohl das Update einer bestehenden Joomla-5-Installation als auch eine Neuinstallation unter Joomla 6 bestehen;
- UTF-8 ohne BOM und korrektes Deutsch für eigene sichtbare Texte verwenden.

## Arbeitsregeln

- Keine Funktionalität oder Gestaltung ohne ausdrücklichen Auftrag verändern.
- Bestehende Benutzeränderungen nicht überschreiben oder zurücksetzen.
- Vor jedem Änderungspaket die betroffenen Dateien nennen.
- Nach jedem Paket PHP-Lint, statische Legacy-Suche und Encoding-Prüfung durchführen.
- Das Originaltemplate aus `Joomla! 4.x Install Files/j51_emani_j4.zip` nur als Vergleichsbasis verwenden.
- Nicht neu beginnen: Der aktuelle Stand wird kontrolliert fertiggestellt.

## Ausgangslage

Das Template enthält 57 PHP- und 7 XML-Dateien. Alle PHP-Dateien bestanden zu Beginn den Syntaxcheck unter PHP 8.3.31; alle XML-Dateien waren wohlgeformt.

Gegenüber der Original-ZIP wurden bereits 39 von 64 PHP-/XML-Dateien inhaltlich geändert. 25 Dateien sind noch unverändert. Die Migration ist daher deutlich fortgeschritten, aber inkonsistent und noch nicht ohne Compat-Plugin lauffähig.

Beim Beginn der Fortsetzung war das Git-Arbeitsverzeichnis bereits nicht sauber. Diese bestehenden Änderungen gehören nicht zu Paket 1 und bleiben unangetastet.

## Festgestellte Joomla-6-Blocker

### Entfernte Konstante `JPATH_PLATFORM`

Vierzehn eigene Formfelder verwenden im installierten Ausgangsstand 2.0.0 `JPATH_PLATFORM` als Ausführungsschutz. Die Konstante ist unter Joomla 6 ohne Compat-Plugin nicht verfügbar. Die Dateien beenden sich deshalb beim Laden.

Betroffen sind:

- `elements/bottomtextpx.php`
- `elements/checkbox.php`
- `elements/googlefonts.php`
- `elements/lefttextpx.php`
- `elements/modules.php`
- `elements/moduleslabel.php`
- `elements/opacitylist.php`
- `elements/opacityslider.php`
- `elements/righttextpx.php`
- `elements/subgroup.php`
- `elements/textpercent.php`
- `elements/textpx.php`
- `elements/toptextpx.php`
- `elements/tplhelper.php`

Der Guard wird auf `defined('_JEXEC') or die;` umgestellt.

### CMS-Filesystem im Installationsskript

`update.php` verwendet noch:

- `Joomla\CMS\Filesystem\File`
- `Joomla\CMS\Filesystem\Folder`

Das CMS-Filesystem wurde unter Joomla 6 in das Compat-Plugin verschoben. Verwendet werden müssen die Framework-Klassen unter `Joomla\Filesystem`. Existenzprüfungen werden mit `is_file()` beziehungsweise `is_dir()` durchgeführt.

### Alte Factory-Zugriffe

`helper.php` enthält noch:

- `Factory::getDbo()`;
- `Factory::getUser()`;
- direkten Zugriff auf `$app->input`.

Die Joomla-6-Alternativen sind:

- `DatabaseInterface` aus dem DI-Container;
- `$app->getIdentity()`;
- `$app->getInput()`.

### Veraltete HTML-Helper

Noch vorhanden sind unter anderem:

- `HTMLHelper::_('behavior.core')`;
- `HTMLHelper::_('jquery.framework')`;
- teilweise `HTMLHelper::_('bootstrap.framework')`.

Diese Aufrufe werden kontrolliert auf den WebAssetManager umgestellt. Bootstrap wird nicht blind entfernt, sondern durch die tatsächlich benötigten Einzelassets ersetzt.

### Alte Document- und Asset-APIs

Mehrere Dateien verwenden noch:

- `Factory::getDocument()`;
- `addScript()`;
- `addStyleSheet()`;
- `addScriptDeclaration()`;
- `addStyleDeclaration()`.

Besonders betroffen sind `index.php`, mehrere Content-Overrides sowie `html/mod_j51layerslideshow/default.php`.

### Manifest- und Assetprobleme

In `templateDetails.xml` und `joomla.asset.json` wurden folgende Probleme festgestellt:

- ein nicht vorhandenes Quellverzeichnis `media` wird referenziert;
- das Wurzelverzeichnis `scss` ist nicht als Template-Datei deklariert;
- die Modulposition `showcase-1a` ist doppelt vorhanden;
- die System-Sprachdatei ist nicht explizit deklariert;
- PHP-Dateien verwenden unterschiedliche Assetnamen;
- generische Namen wie `fontawesome` können kollidieren;
- `template.local-fonts` verweist auf eine nicht vorhandene Datei;
- `composer.json` simuliert weiterhin PHP 5.5.9.

## Umsetzungspakete

### Paket 1: Harte Blocker

1. Vierzehn Formfeld-Guards von `JPATH_PLATFORM` auf `_JEXEC` umstellen.
2. `update.php` auf `Joomla\Filesystem` und `DatabaseInterface` migrieren.
3. `helper.php` auf `getInput()`, `DatabaseInterface` und `getIdentity()` migrieren.
4. Eigene sichtbare Meldungen in den bearbeiteten Dateien korrekt auf Deutsch formulieren.
5. PHP-Lint, Legacy-Suche, Encoding- und Diff-Prüfung durchführen.

### Paket 2: Template-Einstiegspunkte

Betroffene Dateien:

- `index.php`
- `component.php`
- `offline.php`
- `error.php`

Aufgaben:

- alte Factory- und Input-Zugriffe ersetzen;
- `behavior.core` und weitere Compat-Helper entfernen;
- Assetnamen vereinheitlichen;
- doppelte Jarallax-Einbindung entfernen;
- Offline-, Component- und Error-Dokument mit Joomla 6 abgleichen.

### Paket 3: Overrides

Betroffene Bereiche:

- `html/com_content/category`;
- `html/mod_menu`;
- `html/mod_j51layerslideshow`;
- `html/layouts/chromes`.

Jeder Override wird einzeln gegen Joomla 6 geprüft. Das Emani-Markup bleibt erhalten. Entfernt wird ein Override nur nach einer gesonderten funktionalen Entscheidung.

### Paket 4: Backend-Felder und Assetverwaltung

- bestehende Formfeld-Ladung ohne Compat-Plugin testen;
- nur bei nachgewiesener Notwendigkeit auf namespaced Formfelder umstellen;
- Preset-Import, -Export und -Laden testen;
- Media-Dialog und Templateparameter testen;
- einheitliche, kollisionsfreie Assetnamen einführen;
- tote, doppelte und fehlende Assets bereinigen.

### Paket 5: Manifest, Sprache, Tests und Release

- `templateDetails.xml` und Paketstruktur korrigieren;
- Composer-Metadaten aktualisieren;
- eigene sichtbare Texte über Sprachdateien ausgeben;
- UTF-8 ohne BOM und Mojibake prüfen;
- unter Joomla 6 mit deaktiviertem Compat-Plugin testen;
- finales Installations-ZIP aus einer kontrollierten Staging-Struktur bauen;
- genau dieses ZIP in einer frischen Joomla-6-Installation abschließend testen.

## Laufzeittest

Für einen belastbaren Abschluss wird eine isolierte Joomla-6-Testinstallation benötigt. Getestet werden mindestens:

1. Installation und Aktualisierung des Templates;
2. Auswahl als Standardtemplate;
3. Öffnen und Speichern aller Templateparameter;
4. Haupt- und Mobilmenü;
5. alle Modulblöcke und Modul-Chromes;
6. alternative und Masonry-Kategorieansicht;
7. Layer-Slideshow;
8. Offline-Seite;
9. 404- und Fehlerseite;
10. `tmpl=component`;
11. RTL-Ausgabe;
12. Preset-Import, -Export und -Laden;
13. Custom CSS und Custom Code;
14. PHP-, Joomla-, Browserkonsolen- und Netzwerkprotokoll.

Das Plugin **Behaviour – Backward Compatibility** muss während der Abnahme ausdrücklich deaktiviert sein.

## Abschlusskriterien

Die Migration gilt erst als abgeschlossen, wenn:

- keine produktiven Treffer für entfernte Joomla-6-APIs verbleiben;
- alle PHP- und XML-Dateien syntaktisch gültig sind;
- keine templatebedingten PHP- oder JavaScript-Fehler auftreten;
- keine lokalen Assetpfade mit HTTP 404 antworten;
- das Backend alle Parameter anzeigen und speichern kann;
- die definierten Frontendansichten funktionieren;
- das Installations-ZIP keine Entwicklungsdateien enthält;
- das ausgelieferte ZIP in einer frischen Joomla-6-Installation erfolgreich getestet wurde.

## Aktueller Stand

- Audit: abgeschlossen
- Entscheidung Fortsetzung oder Neustart: **Fortsetzung**
- Paket 1: lokal und auf dem Entwicklungsserver abgeschlossen; vierzehn Formfeld-Guards, Filesystem-/Datenbankzugriff und AJAX-Helper wurden migriert
- Paket 2: lokal und auf dem Entwicklungsserver abgeschlossen; die vier Template-Einstiegspunkte wurden auf Joomla-6-native Dokument-, Input- und Asset-APIs umgestellt
- Paket 3: lokal und auf dem Entwicklungsserver abgeschlossen; Content-, Menü-, Slideshow- und Modul-Chrome-Overrides wurden mit dem laufenden Serverstand konsolidiert und Joomla-6-nativ bereinigt
- Laufzeittest: Paket 1 unter Joomla 6.1.2 mit deaktiviertem Compatibility-Plugin erfolgreich
- Laufzeittest: Paket 2 unter Joomla 6.1.2 mit deaktiviertem Compatibility-Plugin erfolgreich
- Laufzeittest: Paket 3 unter Joomla 6.1.2 mit deaktiviertem Compatibility-Plugin erfolgreich

### Verifikation von Paket 1

- alle 57 PHP-Dateien: Syntaxprüfung erfolgreich;
- keine Paket-1-Treffer mehr für `JPATH_PLATFORM`, `Joomla\CMS\Filesystem`, `Factory::getDbo()`, `Factory::getUser()` oder direkten Zugriff auf `$app->input`;
- keine Mojibake-Muster gefunden;
- keine UTF-8-BOMs in den geänderten Dateien;
- die eigentlichen Paket-1-Änderungen: ohne Whitespace-Fehler; im bereits zuvor geänderten `elements/tplhelper.php` besteht außerhalb des Paket-Patches weiterhin ein älterer Trailing-Whitespace-Treffer.

### Server-Laufzeittest von Paket 1 am 18. Juli 2026

Der installierte Emani-Ausgangsstand 2.0.0 wurde anhand von `templateDetails.xml` und `joomla.asset.json` bytegenau dem Paket `j51_emani_j6_migrated_fixed.zip` zugeordnet. Vor dem Upload wurden alle sechzehn Zieldateien per SHA-256 mit diesem Ausgangspaket verglichen. Es gab keine unbekannten Serveränderungen und keine fehlende Datei.

Paket 1 umfasst auf diesem Ausgangsstand sechzehn Dateien:

- vierzehn Formfelder unter `elements/`;
- `helper.php`;
- `update.php`.

Vor der Änderung wurden die sechzehn Serverdateien unter `build/server-backup-paket1-20260718/` gesichert. Die Log-Baseline liegt unter `build/log-baseline-paket1-20260718/`. Übertragen wurden ausschließlich die sechzehn Paketdateien; der anschließende Hashvergleich ergab null Abweichungen.

Das reproduzierbare Testpaket liegt unter `build/j51_emani_2.0.0-paket1-test-v2.zip`. Es basiert auf dem nachgewiesenen Serverpaket 2.0.0 und unterscheidet sich von diesem ausschließlich in den sechzehn Paketdateien. Das Manifest bleibt in diesem Zwischenpaket bewusst bei 2.0.0; die endgültige Versionierung erfolgt in Paket 5.

Erfolgreich geprüft wurden:

- Startseite mit Emani und Layer-Slideshow;
- Alternative-Blog- und Masonry-Blog-Ansicht;
- Component-Ansicht mit `tmpl=component`;
- 404-Fehlerseite;
- Emani-Stilbearbeitung im Backend mit 78 Fieldsets, 312 Inputs, 11 Selects und 6 Textareas;
- read-only Preset-Export über `helper.php` mit gültigem JSON und 219 Templateparametern;
- PHP-Syntax aller 57 Paketdateien;
- wohlgeformtes `templateDetails.xml`;
- keine verbleibenden `JPATH_PLATFORM`-Guards im Paket.

Die Logs nach dem Test liegen unter `build/log-after-paket1-20260718/`. Neu angelegt wurde nur `jcontroller.log.php` mit normalen INFO-Einträgen zum Öffnen des Template-Stils. Es wurden keine neuen Fatal Errors, Exceptions, PHP-Warnungen oder Deprecated-API-Einträge protokolliert.

In der Chrome-Konsole erschien auf der Startseite einmalig `AbortError: Transition was skipped`. Der Hinweis stammt aus einem übersprungenen Slider-Übergang und trat auf den weiteren Testseiten nicht erneut auf. Er ist kein Paket-1-Blocker, soll bei der späteren JavaScript-Prüfung aber erneut bewertet werden.

### Umsetzung und Verifikation von Paket 2

Paket 2 umfasst genau diese vier Template-Einstiegspunkte:

- `index.php`;
- `component.php`;
- `offline.php`;
- `error.php`.

In `index.php` wurden die Joomla-Anwendung und Eingabe über `getApplication()` und `getInput()` angebunden, der WebAssetManager direkt aus dem Template-Dokument bezogen und nicht mehr benötigte Variablen sowie die doppelte Jarallax-Einbindung entfernt. Die vorhandene Variable `$document` bleibt bewusst erhalten, weil sie von eingebundenen Dateien unter `inc/` verwendet wird.

`component.php` lädt die benötigten Template-, Print- und FontAwesome-Styles jetzt über den WebAssetManager. Die alten Aufrufe `behavior.core` und `bootstrap.framework` wurden entfernt. Das bestehende Komponenten-Markup und die Inline-Konfiguration bleiben unverändert.

`offline.php` verwendet ebenfalls den WebAssetManager und bindet die Joomla-6-Login-Erweiterungen über `AuthenticationHelper::getLoginButtons()` ein. Damit können Authentifizierungs-Plugins zusätzliche Schaltflächen wie WebAuthn oder Passkeys bereitstellen. In der aktuellen Testinstallation liefert kein aktiviertes Plugin eine solche Schaltfläche; das normale Benutzername-/Passwortformular funktioniert.

`error.php` verwendet den nativen Input-Zugriff, registrierte Assets und `Uri::root()` für den Startseitenlink. Der Seitentitel wird ausdrücklich aus Fehlercode und Fehlermeldung gesetzt, damit trotz separater Metadaten-Ausgabe weiterhin beispielsweise „Fehler: 404 – Seite nicht gefunden“ erscheint.

Die lokale Verifikation ergab:

- alle 57 PHP-Dateien ohne Syntaxfehler;
- keine Legacy-Treffer in den vier Paketdateien;
- alle acht von den Einstiegspunkten referenzierten lokalen Assetdateien vorhanden;
- keine UTF-8-BOMs oder Mojibake-Muster;
- keine Whitespace-Fehler im Paket-2-Diff.

### Server-Laufzeittest von Paket 2 am 18. Juli 2026

Vor dem Upload wurden die vier Serverdateien unter `build/server-backup-paket2-20260718/` gesichert. Die Log-Baseline liegt unter `build/log-baseline-paket2-20260718/`. Hochgeladen wurden ausschließlich die vier angekündigten Paketdateien. Der anschließende SHA-256-Vergleich zwischen lokalem Stand und Serverkopie ergab keine Abweichung.

Erfolgreich geprüft wurden:

- Startseite, Alternative-Blog und Masonry-Blog mit HTTP 200;
- Komponentenansicht mit `tmpl=component`, Basis- und Print-Styles;
- 404-Ausgabe mit HTTP 404, Fehler-Styles und vollständigem Seitentitel;
- nur eine Jarallax-Skripteinbindung auf der Startseite;
- 90 eindeutige lokale CSS- und JavaScript-Ressourcen ohne HTTP-Fehler;
- Offline-Seite mit dem erwarteten HTTP-Status 503;
- Offline-Styles, FontAwesome, Benutzername- und Passwortfeld;
- 23 Ressourcen der Offline-Seite ohne HTTP-Fehler;
- Rückkehr in den Online-Modus mit HTTP 200 und aktivem Emani-Template.

Der Offline-Modus wurde bewusst im Joomla-Backend umgeschaltet. Ein temporärer Schreibzugriff auf `configuration.php` wurde nicht eingerichtet. Der SFTP-Benutzer konnte die Datei nicht verändern; die Installation blieb bei diesem fehlgeschlagenen Schreibversuch unverändert.

Die Protokolle vor dem Offline-Test liegen unter `build/log-after-paket2-preoffline-20260718/`, die anschließend erneut heruntergeladenen Protokolle unter `build/log-after-paket2-offline-20260718/`. Alle sieben Logdateien waren bytegleich; es gab keine neuen Fatal Errors, Exceptions, PHP-Warnungen oder Deprecated-API-Einträge.

Die Laufzeittests erfolgten über die tatsächlich gerenderte HTTP-Ausgabe einschließlich aller lokalen CSS- und JavaScript-Ressourcen. Chrome war während Paket 2 nicht mit der Codex-Browsersteuerung verbunden; deshalb wurde in diesem Paket kein neuer visueller Browser- oder Konsolentest protokolliert. Dieser Punkt bleibt Bestandteil der abschließenden Browserprüfung in Paket 5.

### Umsetzung und Verifikation von Paket 3

Paket 3 prüft die Overrides in diesen vier Bereichen:

- `html/com_content/category`;
- `html/mod_menu`;
- `html/mod_j51layerslideshow`;
- `html/layouts/chromes`.

Vor der Bearbeitung wurde festgestellt, dass elf lokale Overrides inhaltlich von den bereits weiterentwickelten Dateien auf dem Testserver abwichen. Diese Abweichungen bestanden schon vor Paket 3 und betrafen nicht die zuvor hochgeladenen Pakete. Der aktuelle Serverstand wurde deshalb unter `build/server-baseline-paket3-20260718/` gesichert und als Ausgangspunkt der Konsolidierung verwendet. Dadurch bleiben bereits vorhandene Verbesserungen wie Escaping, sichere HTML-Tag-Namen, eindeutige Slideshow-IDs und `noopener noreferrer` erhalten.

Inhaltlich geändert wurden elf Dateien:

- `html/com_content/category/alternative.php`;
- `html/com_content/category/alternative_children.php`;
- `html/com_content/category/alternative_item.php`;
- `html/com_content/category/masonry.php`;
- `html/com_content/category/masonry_item.php`;
- `html/com_content/category/masonry_links.php`;
- `html/mod_menu/default_heading.php`;
- `html/mod_menu/default_separator.php`;
- `html/mod_j51layerslideshow/default.php`;
- `html/layouts/chromes/mod_simple.php`;
- `html/layouts/chromes/mod_standard.php`.

`alternative_children.php` bezieht Sprache und Benutzer jetzt über `getLanguage()` und `getCurrentUser()` aus der Joomla-View. Die entfernten Aufrufe `Factory::getLanguage()` und `Factory::getUser()` sind nicht mehr vorhanden.

`masonry.php` lädt die registrierten Template-Assets `template.imagesloaded` und `template.masonry` über den WebAssetManager. Initialisierung und Line-Clamp-CSS werden mit `addInlineScript()` und `addInlineStyle()` ausgegeben. Die alten Document-Methoden `addScriptDeclaration()` und `addStyleDeclaration()` wurden entfernt.

Das Layer-Slideshow-Override registriert und aktiviert seine beiden Skripte und Styles über den WebAssetManager. Alle modulabhängigen JavaScript- und CSS-Blöcke werden als eindeutig benannte Inline-Assets ausgegeben. Markup, Animationen und Darstellung bleiben erhalten; mehrere Modulinstanzen verwenden weiterhin voneinander getrennte IDs und Funktionsnamen.

Die Menü-Teillayouts und Modul-Chromes wurden mit dem laufenden Serverstand konsolidiert. Dabei bleiben die Emani-spezifischen `menumod`-Positionen, Parent-Indikatoren, Titelaufteilung und Chrome-Struktur unverändert. Ausgabeattribute, dynamische Tag-Namen und sichtbare Titel werden sicher behandelt.

Inhaltlich unverändert blieben:

- `html/com_content/category/alternative.xml`;
- `html/com_content/category/masonry.xml`;
- `html/mod_menu/default.php`;
- `html/mod_menu/index.html`.

Die beiden XML-Dateien sind wohlgeformt. Die beiden Menüdateien unterschieden sich zwischen lokalem und installiertem Stand nur durch Zeilenenden und enthielten keinen Paket-3-Kompatibilitätsfehler.

Die lokale Verifikation ergab:

- alle 57 PHP-Dateien ohne Syntaxfehler;
- keine Paket-3-Treffer für entfernte Factory-, Document-, Input-, Filesystem- oder Legacy-Aufrufe;
- beide Layout-XML-Dateien wohlgeformt;
- keine UTF-8-BOMs oder Mojibake-Muster;
- keine Whitespace-Fehler im Paket-3-Diff.

### Server-Laufzeittest von Paket 3 am 18. Juli 2026

Die Log-Baseline wurde vor dem Upload unter `build/log-baseline-paket3-20260718/` abgelegt. Hochgeladen wurden ausschließlich die elf angekündigten Override-Dateien. Die anschließend heruntergeladenen Kontrollkopien liegen unter `build/server-verify-paket3-20260718/`; der SHA-256-Vergleich ergab null Abweichungen.

Erfolgreich geprüft wurden:

- Startseite, Alternative-Blog und Masonry-Blog jeweils mit HTTP 200;
- 19 gerenderte Artikelausgaben in der Alternative-Ansicht;
- aktive Masonry-Initialisierung und Line-Clamp-CSS;
- eine Layer-Slideshow mit zusammenpassenden Container- und Steuerungs-IDs;
- keine doppelten HTML-IDs auf der Startseite;
- geladene Tiny-Slider-, ImagesLoaded- und Slideshow-Styles;
- zwei Menüausgaben und zwei Untermenüs;
- je zwei tatsächlich gerenderte Menü-Heading- und Separator-Ausgaben;
- aktive Standard-Modul-Chromes ohne unverarbeitete `<jdoc:include>`-Tags;
- 86 eindeutige lokale CSS- und JavaScript-Ressourcen ohne HTTP-Fehler;
- alle Inline-JavaScript-Blöcke der drei Testseiten ohne Syntaxfehler.

Die nach den Tests erneut heruntergeladenen sieben Protokolle liegen unter `build/log-after-paket3-20260718/`. Sie sind bytegleich mit der Paket-3-Baseline. Es gab keine neuen Fatal Errors, Exceptions, PHP-Warnungen oder Deprecated-API-Einträge.

Wie in Paket 2 erfolgte die Laufzeitprüfung über die gerenderte HTTP-Ausgabe, Assets und Protokolle. Der abschließende visuelle Browser- und Konsolentest bleibt Bestandteil von Paket 5.

### Paket 4: Umsetzung und Serverprüfung am 18. Juli 2026

Paket 4 ist lokal umgesetzt und auf dem Joomla-6-Testserver einschließlich Backend-, Export-, Preset- und Importfunktionen geprüft.

Das präzisierte Upgrade-Ziel beeinflusst die Abnahme wesentlich: Presets werden beim Update niemals automatisch angewendet. Bestehende Datensätze in `#__template_styles`, Stilzuordnungen und Parameter müssen erhalten bleiben. In Paket 5 muss zusätzlich nachgewiesen werden, dass eine vorhandene kundenseitige `custom.css` durch das Update nicht unkontrolliert überschrieben wird.

Lokal geändert wurden für Paket 4:

- `helper.php`;
- `elements/checkbox.php`;
- `elements/opacityslider.php`;
- `elements/subgroup.php`;
- `elements/tplhelper.php`;
- `elements/js/script.js`;
- `joomla.asset.json`;
- `html/com_content/category/masonry.php`;
- `inc/custom_css.php`.

Der Preset-Helper prüft jetzt Berechtigung, Stil-ID, Templatezugehörigkeit und JSON-Struktur. Import und Preset-Laden können nur den angeforderten Site-Stil von `j51_emani` aktualisieren. Das Backend-Skript erhält Template und Stil-ID über Joomla-Script-Optionen, deklariert jQuery und Joomla-Core als WebAsset-Abhängigkeiten und verwendet korrektes Deutsch für eigene sichtbare Meldungen.

Die Referenz auf die nicht vorhandene Datei `elements/js/ibutton.js` wurde aus dem ungenutzten Checkbox-Feld entfernt. Der unnötige Aufruf `jquery.framework` im Opacity-Slider wurde entfernt. Eigene dynamische Assets verwenden kollisionsärmere `j51.*`-Namen.

Das laut Manifest notwendige, lokal jedoch fehlende Verzeichnis `media/` wurde aus den aktuellen Verzeichnissen `css`, `fonts`, `images`, `js` und `scss` rekonstruiert. Es umfasst 206 Dateien. Die fünf Quell- und Media-Bäume sind derzeit hashgleich. Damit kann das spätere ZIP die von `joomla.asset.json` referenzierten Template-Medien tatsächlich installieren beziehungsweise aktualisieren.

`inc/fonts.php` wurde bewusst nicht in Paket 4 aufgenommen. Die schon vorher vorhandene lokale Version entfernt die derzeit auf dem Server verwendete Google-Fonts-Einbindung und findet keine `css/local-fonts.css`. Ein Upload würde daher die Typografie verändern. Diese vorbestehende Abweichung muss vor dem finalen ZIP in Paket 5 ausdrücklich entschieden werden.

Die lokale Verifikation ergab:

- alle 57 PHP-Dateien ohne Syntaxfehler;
- `elements/js/script.js` ohne JavaScript-Syntaxfehler;
- gültiges `joomla.asset.json` ohne doppelte Assetnamen;
- alle templateeigenen Registry-Assets vorhanden;
- keine Paket-4-Legacy-Treffer;
- keine UTF-8-BOMs, Mojibake- oder Diff-Fehler.

Auf den Testserver wurden ausschließlich die neun angekündigten Dateien übertragen. Die Ausgangsdateien liegen unter `build/server-backup-paket4-20260718/`, die Log-Baseline unter `build/log-baseline-paket4-20260718/` und die hashgeprüften Kontrollkopien unter `build/server-verify-paket4-20260718/`.

Bei der ersten Registry-Bereinigung wurden die auf dem Server weiterhin benötigten Kompatibilitätsaliase `fontawesome.joomla` und `template.jquery` zu früh entfernt. Dadurch war im Frontend vorübergehend ein HTTP-500-Fehler sichtbar. Der Fehler wurde automatisiert erkannt und vom Benutzer per Screenshot bestätigt. Die Registry wurde korrigiert: Die bestehenden Server-Aliase bleiben erhalten, während Masonry und ImagesLoaded intern zusätzlich die namespaced Assets `j51.template.masonry` und `j51.template.imagesloaded` verwenden.

Nach der Korrektur wurden erfolgreich geprüft:

- Startseite mit HTTP 200;
- Masonry-Blog mit HTTP 200 und aktiver Masonry-Initialisierung;
- keine Registry-, PHP- oder Deprecated-Ausgabe;
- 41 Startseiten- und 39 Masonry-Ressourcen ohne HTTP-Fehler;
- Joomla-FontAwesome aus `/media/system/` sowie die Emani-FontAwesome-Dateien und das vorhandene jQuery-Asset.

Die zunächst abgewiesenen Backend-Anmeldungen waren kein Server- oder Passwortproblem. In der damaligen Zugangsdaten-Datei stand die Superuser-Zeile als `user Codi` ohne Doppelpunkt. Der Testparser übernahm deshalb irrtümlich die gesamte Zeile als Benutzernamen. Nach der Korrektur auf ein eindeutig beschriftetes `User:`-Feld funktionierte die Anmeldung mit dem unveränderten Passwort. Eine Passwortänderung war nicht erforderlich.

Die Backendprüfung ergab:

- Emani-Template-Stil 13 lässt sich fehlerfrei öffnen;
- unverändertes Speichern wird von Joomla mit „Der Stil wurde gespeichert.“ bestätigt;
- der Export liefert ein gültiges JSON-Objekt mit 219 Template-Parametern;
- `Example Style 1` lässt sich laden und verändert den Stil nachweislich auf einen anderen Stand mit 216 Parametern;
- der zuvor exportierte Originalstand lässt sich über die Importfunktion wieder einspielen;
- der abschließende Export enthält wieder 219 Parameter und ist semantisch identisch mit dem ursprünglichen Export;
- ein Exportversuch mit dem fremden Cassiopeia-Stil 11 wird vom Helper korrekt abgelehnt.

Die Browsertests wurden mit einer echten Joomla-Superuser-Sitzung durchgeführt. Die drei dabei erzeugten Preset-Downloads wurden nach der erfolgreichen Gleichheitsprüfung wieder aus dem Download-Verzeichnis entfernt. Der Emani-Stil befindet sich auf dem Server wieder exakt im ursprünglichen Parameterzustand.

Der abschließende SFTP-Logstand liegt unter `build/log-after-paket4-backend-20260718/`. Sechs der acht Logdateien sind gegenüber dem vorherigen Paket-4-Stand bytegleich. `error.php` enthält zusätzlich nur einen INFO-Eintrag aus dem zuvor falsch geparsten Anmeldeversuch. `jcontroller.log.php` enthält acht normale INFO-Einträge zum Öffnen und Bearbeiten von Template-Stil 13. Es wurden keine neuen Fatal Errors, Exceptions, PHP-Warnungen, Deprecated-API- oder WebAsset-Fehler protokolliert.

### Paket 5: Releasebau, Update- und Neuinstallationsabnahme am 18. Juli 2026

Paket 5 ist vollständig umgesetzt und geprüft. Das vorrangige Produktziel – ein Update des vorhandenen Templates `j51_emani` mit Erhalt der Kundendaten – wurde mit dem tatsächlich erzeugten Release-ZIP erfolgreich getestet. Zusätzlich wurde genau dieses ZIP nach einer vollständigen Emani-Deinstallation als echte Neuinstallation abgenommen.

Für Paket 5 wurden folgende Quelldateien geändert oder ergänzt:

- `inc/fonts.php`: Die konfigurierbaren Google-Fonts werden weiterhin unterstützt, aber über eindeutig benannte Assets des WebAssetManagers registriert und geladen.
- `inc/scripts.php`: Jarallax wird erst nach dem Laden der Seite und nur bei verfügbarer Bibliothek initialisiert. Die Ladebedingung wertet jetzt die acht tatsächlich vorhandenen Parallax-Parameter aus.
- `helper.php`, `elements/tplhelper.php` und `elements/js/script.js`: Eigene Meldungen werden über Joomla-Sprachschlüssel ausgegeben.
- `language/en-GB/en-GB.tpl_j51_emani.ini` und `language/en-GB/en-GB.tpl_j51_emani.sys.ini`: Englische Meldungen und korrigierte Emani-Sprachpräfixe.
- `language/de-DE/de-DE.tpl_j51_emani.ini` und `language/de-DE/de-DE.tpl_j51_emani.sys.ini`: Neue deutsche Template- und Systemsprachdateien.
- `templateDetails.xml`: Paketdatum, Sprachdateien, Presetauswahl und doppelte Modulposition korrigiert; Upgrade-Methode und Templateidentität bleiben erhalten.
- `composer.json` und `composer.lock`: Projekt-, Lizenz- und PHP-Metadaten aktualisiert. Beide Dateien gehören nicht zum installierbaren Release-ZIP.
- `scripts/build-release.ps1`: Reproduzierbarer Aufbau einer kontrollierten Staging-Struktur und des Release-ZIPs ausschließlich aus den im Manifest freigegebenen Bestandteilen.

Der Releasebau schließt `css/custom.css` und `media/css/custom.css` ausdrücklich aus. Dadurch überschreibt das Update keine kundenseitigen Anpassungen an diesen beiden vorgesehenen Speicherorten. Entwicklungsdateien, Dokumentation, Buildskripte, Git-Daten und Composer-Dateien sind ebenfalls nicht Bestandteil des ZIPs.

Das derzeitige Release-Artefakt ist:

- Datei: `build/j51_emani_2.1.0-joomla6.zip`;
- Größe: 4.926.902 Bytes;
- ZIP-Einträge: 467;
- SHA-256: `F38ADFE241C9B507E63679433435561CFAC66C23C2CE47A4D4FD4C096353A2E7`.

Die lokale Schlussprüfung dieses Stands ergab:

- alle 57 PHP-Dateien ohne Syntaxfehler;
- JavaScript-, JSON-, XML- und PowerShell-Syntax gültig;
- alle vier Sprachdateien als INI lesbar;
- keine UTF-8-BOMs oder Mojibake-Muster;
- 74 eindeutige Modulpositionen ohne Duplikate;
- keine falschen `TPL_J51_LAYLA_*`-Sprachschlüssel;
- keine produktiven Treffer für die gesuchten entfernten Joomla-APIs; zwei Suchtreffer benennen bereits ersetzte Aufrufe lediglich in Kommentaren;
- keine Whitespace-Fehler im Git-Diff;
- Manifest und Installationsskript liegen an der Wurzel des ZIPs;
- keine verbotenen Entwicklungs- oder Custom-CSS-Dateien im ZIP.

#### Updateprüfung mit dem Release-ZIP

Genau das kontrolliert erzeugte ZIP wurde über das Joomla-Backend als Update des vorhandenen Emani-Templates installiert. Die Testinstallation meldet Joomla 6.1.2; das Plugin **Behaviour – Backward Compatibility** blieb deaktiviert.

Vor dem Update wurden die 219 Parameter des Emani-Stils 13 exportiert. Zusätzlich wurden an beiden vorgesehenen Custom-CSS-Speicherorten unterschiedliche Testmarken angelegt. Nach der Installation waren:

- alle 219 Stilparameter semantisch unverändert;
- beide Custom-CSS-Testdateien bytegenau unverändert;
- die geprüften Paketdateien auf dem Server hashgleich zur Release-Staging-Struktur;
- Templateidentität, Stil-ID und Updatepfad erhalten.

Die Testmarken wurden anschließend wieder entfernt, weil an beiden Orten vor dem Test keine `custom.css` vorhanden war. Die Serverausgangsdateien und Kontrollkopien liegen unter `build/server-backup-paket5-20260718/`, `build/server-verify-paket5-update-20260718/` und `build/server-verify-paket5-final-20260718/`.

#### Browser-, Asset- und Logprüfung

Der erste Paket-5-Browsertest zeigte `ReferenceError: jarallax is not defined`. Die Ursache lag in einer zu frühen Initialisierung und einer falschen Ladebedingung in `inc/scripts.php`. Beides wurde korrigiert, das ZIP neu gebaut und erneut als Update installiert.

Mit dem korrigierten Stand wurden Startseite, Alternative-Blog, Masonry-Blog, Komponentenansicht und 404-Seite in einem echten Browser geprüft. Alle Seiten wurden korrekt gerendert und erzeugten keine Konsolenfehler. Der Emani-Stil ließ sich im Backend unverändert speichern; die neuen deutschen JavaScript-Sprachtexte waren registriert.

Die Browser-Ressourcenprüfung erfasste 48 Seitenassets. Sechs Joomla-Bild-URLs mit einem angehängten `#joomlaImage://`-Fragment wurden vom Downloadprüfer wegen fehlender Content-Type-Angabe als nicht heruntergeladen gemeldet; dieselben Bilder wurden im Browser sichtbar und fehlerfrei dargestellt. Es gab keine fehlgeschlagenen Stylesheets, Skripte oder Fonts.

Die acht abschließend heruntergeladenen Logs liegen unter `build/log-after-paket5-update-20260718/`. Sieben Dateien sind gegenüber dem Paket-4-Stand bytegleich. Nur `jcontroller.log.php` enthält zusätzliche normale Einträge vom Öffnen und Speichern des Template-Stils. Es wurden keine neuen Deprecated-, Fatal-, Uncaught-, PHP-Warning-, PHP-Notice-, Exception- oder WebAsset-Meldungen gefunden.

#### Neuinstallation und Wiederherstellung

Nach ausdrücklicher Freigabe wurde der vorhandene Emani-Stil vor der Deinstallation erneut exportiert. Die Sicherung unter `build/fresh-install-backup-20260718/style-13-before-fresh-install.json` enthält 219 Parameter. Emani war Standard für alle Seiten; einzelne Menüzuordnungen bestanden deshalb nicht.

Cassiopeia wurde vorübergehend als Standard gesetzt. Anschließend wurde `j51_emani` über die Joomla-Erweiterungsverwaltung vollständig deinstalliert. Joomla bestätigte die Deinstallation und lieferte bei einer anschließenden Suche keine Emani-Erweiterung mehr. Damit war ausgeschlossen, dass die folgende Installation lediglich als weiteres Update ausgeführt wurde.

Genau das oben dokumentierte Release-ZIP wurde danach über die Joomla-Paketinstallation hochgeladen. Joomla bestätigte die frische Template-Installation. Die Installation legte einen neuen Template-Stil mit ID 15 an; der alte Stil 13 war durch die Deinstallation erwartungsgemäß entfernt worden.

Der neue Stil ließ sich zunächst mit den ausgelieferten Standardparametern öffnen, unverändert speichern und als Standard für alle Seiten aktivieren. Die Startseite wurde mit Slideshow, Navigation, Modulen und Inhalten korrekt gerendert; es gab keine Browserkonsolenfehler.

Danach wurden die 219 gesicherten Parameter über die Emani-Importfunktion in Stil 15 eingespielt. Ein erneuter Export enthielt wieder 219 Parameter und war semantisch vollständig identisch mit der Sicherung. Emani ist wieder Standard für alle Seiten.

Mit dem wiederhergestellten Stand wurden erneut geprüft:

- Startseite;
- Alternative-Blog;
- Masonry-Blog;
- Komponentenansicht mit `tmpl=component` und der erwarteten Body-Klasse `contentpane component`;
- 404-Seite mit dem Titel „Fehler: 404 - Seite nicht gefunden“;
- keine Browserkonsolenfehler auf den geprüften Seiten.

Fünf repräsentative Serverdateien – Manifest, Helper, Jarallax-Skripteinbindung, Asset-Registry und deutsche Sprachdatei – sind nach der Neuinstallation hashgleich zur Release-Staging-Struktur. Weder unter `templates/j51_emani/css` noch unter `media/templates/site/j51_emani/css` wurde bei der Neuinstallation eine `custom.css` angelegt. Die Kontrollkopien liegen unter `build/server-verify-paket5-freshinstall-20260718/`.

Die sieben Serverlogs nach Deinstallation, Neuinstallation, Stilimport und Frontendtests liegen unter `build/log-after-paket5-freshinstall-20260718/`. Sechs Dateien sind gegenüber dem Paket-5-Update-Stand bytegleich. Nur `jcontroller.log.php` enthält zusätzliche normale Backendaktivität. Die Suche ergab keine Deprecated-, Fatal-, Uncaught-, PHP-Warning-, PHP-Notice-, Exception- oder WebAsset-Meldungen.

Damit sind sowohl der maßgebliche Updatepfad von einem vorhandenen Joomla-5-Emani als auch die frische Installation des Release-ZIPs unter Joomla 6.1.2 nachgewiesen. Paket 5 und die geplante Joomla-6-Migration sind abgeschlossen.

## Release 6.2.1 (November 2026)

Gemeinsamer Release mit den Migrationen von Nina und Layla; alle drei Templates tragen ab jetzt dieselbe Versionsnummer.

Geprüft gegen Joomla 6.2.0 (Core-Quellcode) und auf der Testinstallation unter Joomla 6.2.0 mit PHP 8.4, Plugin **Behaviour – Backward Compatibility 6** deaktiviert. PHP-Lint unter PHP 8.3, statische Legacy-Suche ohne Treffer, alle `Text::_()`-Schlüssel gegen die Joomla-6-Sprachdateien abgeglichen.

Änderungen:

- `html/com_content/category/masonry.php`: Skripte `j51.template.imagesloaded` und `j51.template.masonry` werden selbst registriert, falls Emanis `joomla.asset.json` nicht geladen ist. Vorher führte ein Menüpunkt mit Layout `j51_emani:masonry` unter einem anderen Standard-Template zu Fehler 500 (`UnknownAssetException`).
- `inc/style.php`: Core-Asset `fontawesome` wieder aktiv (war als „Duplikat“ entfernt worden). Ohne dieses Asset fehlen die Joomla-`icon-*`-Klassen, z. B. die Bearbeiten-Symbole an Modulen im Frontend.
- `css/base/template.css`, `media/css/base/template.css`: farbige Status-Auswahl (`form-select-color-state`) im Frontend-Beitragsformular lesbar; das weiße `select-bg.svg` verdeckte den Hintergrund, die weiße Schrift war unsichtbar.
- `error.php`: Sprachschlüssel `JERROR_LAYOUT_MIS_TYPED_ADDRESS` → `JERROR_LAYOUT_MISTYPED_ADDRESS` (in Joomla 6 umbenannt).
- `update.php`: Bereinigung alter Override-Ordner und -Dateien nur noch beim Update von Versionen vor 2.0.0 (Vorgängerversion wird im `preflight` aus `manifest_cache` gelesen). Bei neueren Ständen enthalten diese Ordner nur noch kundeneigene Overrides.
- `elements/infobox.php`, `noticebox.php`, `titlebox.php`: `\defined('_JEXEC') or die;` statt `JPATH_BASE`-Prüfung.
- `templateDetails.xml`: Version 6.2.1, Autor „Joomla51 / Alpenrand Digital“, Hinweis „Project by Alpenrand Digital“ auf dem Details-Reiter (Fieldset `basic`, Sprachschlüssel `TPL_J51_EMANI_ALPENRAND_NOTE` in de-DE und en-GB).
- Arbeitsdateien `dateiliste.txt` / `dateiliste.txt.bak` (enthielten lokale Pfade) entfernt.

Laufzeittest: Update von 6.2.1-Vorstand auf der Testinstallation, Startseite, Alternative-Blog, Masonry-Blog, Beitragsansicht, Suche (Smart Search), 404-Seite ohne PHP-Fehler; Frontend-Bearbeitung mit Speichern; Browserkonsole ohne Fehler aus dem Template.

