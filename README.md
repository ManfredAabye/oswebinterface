# oswebinterface

Achtung der neue Evats Kalender ist nicht kompatibel zum alten, alle einträge müssen erneuert werden.

Eine anleitung zum Evats Kalender gibt es hier: <https://github.com/ManfredAabye/oswebinterface/blob/main/Events.md>

<img src="https://raw.githubusercontent.com/ManfredAabye/oswebinterface/refs/heads/main/include/oswebinterfaceK1.png" alt="Project Badge" width="680">

**OpenSimulator Viewer Webinterface:**

Dieses Webinterface dient ausschließlich dazu, die Kommunikationslücke zwischen dem OpenSimulator und dem Viewer/Client, wie beispielsweise Firestorm, zu schließen.
Es ermöglicht eine nahtlose Interaktion und erleichtert die Verwaltung und Steuerung der virtuellen Umgebung direkt über den Viewer.

Das normale Webinterface wird in der Regel separat installiert und bietet zusätzliche Funktionen zur Verwaltung des OpenSimulator-Servers.
Mittlerweile gibt es hierfür einige ansprechende und benutzerfreundliche Lösungen, die die Administration und Konfiguration des Systems erheblich vereinfachen.
Diese Tools sind besonders nützlich für Benutzer, die keine tiefergehenden technischen Kenntnisse besitzen, aber dennoch effizient mit der Plattform arbeiten möchten.

Für das osWebinterface ist keine Domain erforderlich – eine einfache IP-Adresse genügt, genau wie beim OpenSimulator selbst. Das Interface lässt sich ohnehin nicht direkt über einen normalen Aufruf im Browser öffnen.
Natürlich wirkt eine eigene Domain ansprechender und professioneller, und sie ist heutzutage auch recht kostengünstig.
Das osWebinterface wurde so gestaltet, dass es sich problemlos in gängige CMS-Systeme wie WordPress oder Joomla integrieren lässt – beispielsweise als Custom HTML Block, iFrame, Embed Block oder sogar als eigenständige Seite.

## Installation Instructions

Bitte konfigurieren sie Ihr osWebinterface wie folgt:

  1. Laden Sie das oswebinterface in Ihr Webverzeichnis hoch (z.B. /var/www/oswebinterface).
  2. Passen Sie die Konfigurationsdateien an Ihre Umgebung an.
  3. Stellen Sie sicher, dass Ihr Webserver (Apache, Nginx, etc.) korrekt eingerichtet ist, um PHP-Dateien auszuführen.
  4. Richten Sie die erforderlichen Berechtigungen für die Dateien und Verzeichnisse ein.

    Folgende Dateien müssen angepasst werden:
      oswebinterface/include/config.php
      oswebinterface/include/env.php
      opensim/bin/Robust.HG.ini

 5. Testen Sie das Interface, indem Sie die URL in Ihrem Browser aufrufen (z.B. <http://IhreDomainOderIP/oswebinterface/welcomesplashpage.php>).

## Config.php Setup

config.example.php umbenennen in config.php und folgende Einträge anpassen:

Die config.example.php (Config.php) wird in der [Config.md](./Config.md) ausführlich erklärt.

## Env.php Setup

Die Datei `env.php` enthält alle sensiblen Zugangsdaten und Umgebungsvariablen für das Webinterface. Sie sollte niemals öffentlich zugänglich sein!

**Wichtige Einstellungen in env.php:**

- **DB_SERVER**: Adresse des Datenbankservers (meistens `localhost`).
  > Beispiel: `'DB_SERVER', 'localhost'` – Nur ändern, wenn die Datenbank auf einem anderen Server läuft.
- **DB_USERNAME**: Benutzername für die Datenbank.
  > Beispiel: `'DB_USERNAME', 'dein_benutzer'` – Trage hier den MySQL/MariaDB-Benutzernamen ein.
- **DB_PASSWORD**: Passwort für die Datenbank.
  > Beispiel: `'DB_PASSWORD', 'dein_passwort'` – Das zugehörige Passwort zum Datenbankbenutzer.
- **DB_NAME**: Name der Hauptdatenbank.
  > Beispiel: `'DB_NAME', 'deine_datenbank'` – Hier steht der Name der OpenSim-Datenbank.
- **DB_ASSET_NAME**: Name der Asset-Datenbank.
  > Beispiel: `'DB_ASSET_NAME', 'deine_assetdatenbank'` – Kann identisch mit DB_NAME sein, wenn Assets in derselben Datenbank liegen.

- **REMOTEADMIN_HTTPAUTHUSERNAME**: Benutzername für die RemoteAdmin-HTTP-Authentifizierung.
  > Beispiel: `'REMOTEADMIN_HTTPAUTHUSERNAME', 'opensim'` – Wird für die Kommunikation mit dem OpenSim-Server benötigt.
- **REMOTEADMIN_HTTPAUTHPASSWORD**: Passwort für die RemoteAdmin-HTTP-Authentifizierung.
  > Beispiel: `'REMOTEADMIN_HTTPAUTHPASSWORD', 'opensim123'` – Unbedingt ein sicheres Passwort wählen!

**Hinweise:**

- Nach dem Anpassen der Werte die Datei speichern und die Rechte so setzen, dass sie nicht öffentlich im Web erreichbar ist (z.B. chmod 600).
- Die Zugangsdaten müssen mit denen in der OpenSim-Konfiguration übereinstimmen.
- Bei Problemen mit der Datenbankverbindung prüfe Benutzername, Passwort und Serveradresse.

## Robust.ini Setup

    MapTileURL = "${Const|BaseURL}/oswebinterface/maptile.php";
    SearchURL = "${Const|BaseURL}/oswebinterface/search.php"
    DestinationGuide = "${Const|BaseURL}/oswebinterface/guide.php"
    AvatarPicker = "${Const|BaseURL}/oswebinterface/avatarpicker.php"
    GridSearch = "${Const|BaseURL}/oswebinterface/gridsearch.php";
    MessageURI = ${Const|BaseURL}/oswebinterface/messages.php
    welcome = ${Const|BaseURL}/oswebinterface/welcomesplashpage.php
    economy = ${Const|BaseURL}:8008/; Download, compile and install Moneyserver from here: https://github.com/ManfredAabye/opensimcurrencyserver-dotnet
    about = ${Const|BaseURL}/oswebinterface/aboutinformation.php
    register = ${Const|BaseURL}/oswebinterface/createavatar.php
    help = ${Const|BaseURL}/oswebinterface/help.php
    password = ${Const|BaseURL}/oswebinterface/passwordreset.php
    partner = ${Const|BaseURL}/oswebinterface/partner.php
    GridStatus = ${Const|BaseURL}/oswebinterface/gridstatus.php
    GridStatusRSS = ${Const|BaseURL}/oswebinterface/gridstatusrss.php

  ---

  




