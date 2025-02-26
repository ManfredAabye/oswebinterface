# oswebinterface
Ein Webinterface für die kommunikation zwischen Viewer und OpenSimulator

Funktionen

Verbessert die Kommunikation zwischen OpenSimulator und Firestorm Viewer

Installation

E-Mail installieren zum Beispiel:

Code:
sudo apt-get install sendmail

Kopiere die Dateien auf deine Webseite.
Benenne config.example.php in config.php um und fülle sie mit den notwendigen Informationen aus.

    define('DB_SERVER', 'localhost'); // In der Regel bleibt das so.
    define('DB_USERNAME', 'your_username'); // Dein SQL Datenbank Benutzername.
    define('DB_PASSWORD', 'your_password'); // Dein SQL Datenbank Passwort.
    define('DB_NAME', 'your_database'); // Dein SQL Datenbankname.
    define('DB_ASSET_NAME', 'your_database'); // Das kann auch die gleiche wie DB_NAME sein.
    define('BASE_URL', 'http://yourdomain.com'); // Deine Server Adresse 127.0.0.1 oder 192.168.2.100 oder meinewebseite.com.
    define('SITE_NAME', 'Dein Grid Name'); // Meine Virtuelle Welt.

Konfiguration des OpenSimulator

Öffne deine Robust.ini und konfiguriere sie wie folgt:

    MessageURI = ${Const|BaseURL}/messages.php
    welcome = ${Const|BaseURL}/welcomesplashpage.php
    economy = ${Const|BaseURL}:8008/
    about = ${Const|BaseURL}/aboutinformation.php
    register = ${Const|BaseURL}/createavatar.php
    help = ${Const|BaseURL}/help.php
    password = ${Const|BaseURL}/passwordreset.php
    partner = ${Const|BaseURL}/partner.php
    GridStatus = ${Const|BaseURL}:${Const|PublicPort}/gridstatus.php
    GridStatusRSS = ${Const|BaseURL}:${Const|PublicPort}/gridstatusrss.php

Konfiguration des Viewers

Einstellungen - OpenSim: Ihr müst euer Grid neu anlegen oder Aktualisieren.

Einstellungen - Netzwerk: Hier müst ihr euren Webbrowser Cache löschen.
