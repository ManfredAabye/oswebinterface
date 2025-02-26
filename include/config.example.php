<?php
// MySQL Verbindungsdaten
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'your_database');
define('DB_ASSET_NAME', 'your_database');

// Seitenadressen
define('BASE_URL', 'http://yourdomain.com');
define('SITE_NAME', 'Dein Grid Name');

// Konfigurieren sie ihre Robust.ini wie folgt:
//
// MessageURI = "${Const|BaseURL}/messages.php";
// welcome = ${Const|BaseURL}/welcomesplashpage.php
// economy = ${Const|BaseURL}:8008/
// about = ${Const|BaseURL}/aboutinformation.php
// register = ${Const|BaseURL}/createavatar.php
// help = ${Const|BaseURL}/help.php
// password = ${Const|BaseURL}/passwordreset.php
// partner = ${Const|BaseURL}/partner.php
// GridStatus = ${Const|BaseURL}:${Const|PublicPort}/gridstatus.php
// GridStatusRSS = ${Const|BaseURL}:${Const|PublicPort}/gridstatusrss.php

// Konfigurationsoptionen Standart Template:
// "headerBlanc.php", "headerST.php", "headerW3.php", "headerBT5.php", "headerFoundation.php", 
// "headerMaterialize.php", "headerTailwind.php",  "headerPrimer.php", "headerTachyons.php", 
// "headerSpectre.php", "headerTent.php"
define('HEADER_FILE', 'headerBlanc.php'); // Ändere diesen Wert, um die verschienen Template Header Datei zu laden.

// Konfigurationsoption für den Banker
define('BANKER_UUID', '00000000-0000-0000-0000-000000000000');

// Verifizierungsfunktionen
define('VERIFICATION_METHOD', 'email'); // 'email' oder 'uuid'

// RemoteAdmin
define('REMOTEADMIN_URL', 'localhost');
define('REMOTEADMIN_PORT', 8002);
define('REMOTEADMIN_HTTPAUTHUSERNAME', 'opensim');
define('REMOTEADMIN_HTTPAUTHPASSWORD', 'opensim123');

// Asset Bilder
define('ASSETPFAD', 'cache/');
define('ASSET_FEHLT', ASSETPFAD . '00000000-0000-0000-0000-000000000002');
define('GRID_PORT', ':8002');
define('GRID_ASSETS', ':8003/assets/');
define('GRID_ASSETS_SERVER', 'BASE_URL' . 'GRID_ASSETS');

// Guide
define('GRIDLIST_FILE', 'include/gridlist.csv');
define('GRIDLIST_VIEW', 'json'); // 'json', 'database' oder 'grid'

// Media 
//define('MEDIA_SERVER', 'http://localhost:8500/stream');
define('MEDIA_SERVER', 'http://schwarze-welle.de:7500/stream');
//define('MEDIA_SERVER_STATUS', 'http://localhost:8500/status-json.xsl');
define('MEDIA_SERVER_STATUS', 'http://schwarze-welle.de:7500/');

// Passwörter (unbedingt austauschen!)
// Für das austauschen der Passwöter könnt ihr den paswd_generator.php benutzen den ihr im Verszeichnis /include findet.
$registration_passwords_register = ["bAa5sUj0gZQmwqTI", "AGISwzvNjAl91d9A", "Ve2HTTvqgIXtzZpN", "tmuglKrrH7ryiK6r", "d6jj9DDJ6op0UKDN"];
$registration_passwords_reset = ["EBltIFWpOHfTJhkm", "Y4WMWsZLZxHTjJiA", "5dqsn3H5dJChEMCv", "zb4gIohq8EP95FGb", "ntujqwVCxQdZZeiF"];
$registration_passwords_partner = ["AR7BDtcpdjSJimTe", "dnO82wgGMYvQYBjT", "24swU9boBSk8G1oL", "48ceU4z8GorHYKN2", "M2H4JfTZFrIPJGRk"];
$registration_passwords_inventory = ["ABBtzu9zQbAIfYWa", "OjBVn7ggaE8dtiJz", "0qFaXnus26Aocra5", "jTICpDYF6QUdLfNA", "hx0rRRGbGGStRi7S"];
$registration_passwords_datatable = ["pESw0shb8yhsGO8B", "YtXhlcZi10DvpwfB", "lBQTfCdXX22SNXE3", "qOWVtQtubpkFKSwO", "nBsb35CmP9lpbRju"];
$registration_passwords_listinventar = ["Z3TfMz6pxYrlL0Wx", "sX9IKGdPJ9aFxu4w", "ZgbTyvbGxN8Dgepc", "4UjwuuoVQ6qavo1t", "8Ul2jl7TY54Hj15j"];
$registration_passwords_picreader = ["3XW5DURvJfEMG8KV", "IQmh5VJGLmsJX9R7", "BU2DstTOfYeWKv6l", "dI8L9tZ9ZqJ2akQy", "7GWpOGxx5hF4QHY3"];
$registration_passwords_mutelist = ["Z3vtFiTawIm3Ohm0", "tg8v1VtGaKygGkol", "otHUN3qOl5mR8YVy", "U9jf3UZjWh65svfz", "zvzQByvO5cWQLrf5"];
$registration_passwords_avatarpicker = ["gSAxwhAtbggvzGjf", "lLJEF7T6JtNYJuod", "5q0Zsl59QxA62pLQ", "oedaX0RPYfBNGPZP", "ILCH2lg9cjCumRxZ"];
$registration_passwords_economy = ["9H1H9Ny9rOUd8qGw", "3ZaNoj75CYYe9lTX", "JVPo93v5F4ERNLmB", "8vcX4bIYAtpHzwtd", "cbe439ectfZxwcHn"];

// Farben der Webseite
$colorSchemes = array(
    'oceanBreeze' => array('header' => '#2E8BC0', 'footer' => '#2E8BC0', 'secondary' => '#B1D4E0', 'primary' => '#3B3B98', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'sunsetGlow' => array('header' => '#FF6F61', 'footer' => '#FF6F61', 'secondary' => '#FFD54F', 'primary' => '#2C3E50', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'forestHaven' => array('header' => '#2F5233', 'footer' => '#2F5233', 'secondary' => '#A4DE02', 'primary' => '#FFAE03', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'lavenderBliss' => array('header' => '#6A0572', 'footer' => '#6A0572', 'secondary' => '#D2B4DE', 'primary' => '#F5B7B1', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'fierySunset' => array('header' => '#D32F2F', 'footer' => '#D32F2F', 'secondary' => '#FFCDD2', 'primary' => '#B71C1C', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'coolMint' => array('header' => '#009688', 'footer' => '#009688', 'secondary' => '#B2DFDB', 'primary' => '#004D40', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'royalBlue' => array('header' => '#3F51B5', 'footer' => '#3F51B5', 'secondary' => '#C5CAE9', 'primary' => '#1A237E', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'autumnHarvest' => array('header' => '#8D6E63', 'footer' => '#8D6E63', 'secondary' => '#FFCCBC', 'primary' => '#3E2723', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'goldenHour' => array('header' => '#FFEB3B', 'footer' => '#FFEB3B', 'secondary' => '#FFF9C4', 'primary' => '#FBC02D', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'mintChocolate' => array('header' => '#4CAF50', 'footer' => '#4CAF50', 'secondary' => '#CDDC39', 'primary' => '#795548', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'berryBurst' => array('header' => '#E91E63', 'footer' => '#E91E63', 'secondary' => '#F8BBD0', 'primary' => '#880E4F', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'midnightBlue' => array('header' => '#0D47A1', 'footer' => '#0D47A1', 'secondary' => '#BBDEFB', 'primary' => '#1E88E5', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'grayscale1' => array('header' => '#333333', 'footer' => '#333333', 'secondary' => '#666666', 'primary' => '#E0E0E0', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'grayscale2' => array('header' => '#4F4F4F', 'footer' => '#4F4F4F', 'secondary' => '#A0A0A0', 'primary' => '#FFFFFF', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'grayscale3' => array('header' => '#2B2B2B', 'footer' => '#2B2B2B', 'secondary' => '#858585', 'primary' => '#D9D9D9', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'emeraldDream' => array('header' => '#50C878', 'footer' => '#50C878', 'secondary' => '#98FB98', 'primary' => '#006400', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'coralReef' => array('header' => '#FF7F50', 'footer' => '#FF7F50', 'secondary' => '#FFD700', 'primary' => '#8B0000', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'purpleHaze' => array('header' => '#8A2BE2', 'footer' => '#8A2BE2', 'secondary' => '#DA70D6', 'primary' => '#4B0082', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'sunshineMeadow' => array('header' => '#FFD700', 'footer' => '#FFD700', 'secondary' => '#F0E68C', 'primary' => '#556B2F', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'autumnLeaves' => array('header' => '#FF4500', 'footer' => '#FF4500', 'secondary' => '#FFD700', 'primary' => '#8B4513', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'crimsonTide' => array('header' => '#DC143C', 'footer' => '#DC143C', 'secondary' => '#FFDAB9', 'primary' => '#8B0000', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'skylineView' => array('header' => '#87CEEB', 'footer' => '#87CEEB', 'secondary' => '#4682B4', 'primary' => '#1E90FF', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'blazingSunset' => array('header' => '#FF4500', 'footer' => '#FF4500', 'secondary' => '#FFD700', 'primary' => '#FF8C00', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'morningMist' => array('header' => '#87CEEB', 'footer' => '#87CEEB', 'secondary' => '#B0E0E6', 'primary' => '#4682B4', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'twilightSparkle' => array('header' => '#4B0082', 'footer' => '#4B0082', 'secondary' => '#9370DB', 'primary' => '#8A2BE2', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'sereneGreen' => array('header' => '#2E8B57', 'footer' => '#2E8B57', 'secondary' => '#98FB98', 'primary' => '#006400', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'coralBlush' => array('header' => '#FF6F61', 'footer' => '#FF6F61', 'secondary' => '#FFA07A', 'primary' => '#FA8072', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'earthyBrown' => array('header' => '#8B4513', 'footer' => '#8B4513', 'secondary' => '#D2B48C', 'primary' => '#A0522D', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'crimsonWave' => array('header' => '#B22222', 'footer' => '#B22222', 'secondary' => '#FF6347', 'primary' => '#DC143C', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'coolCyan' => array('header' => '#00CED1', 'footer' => '#00CED1', 'secondary' => '#E0FFFF', 'primary' => '#20B2AA', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'deepPurple' => array('header' => '#9400D3', 'footer' => '#9400D3', 'secondary' => '#9932CC', 'primary' => '#8B008B', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'warmAmber' => array('header' => '#FFBF00', 'footer' => '#FFBF00', 'secondary' => '#FFD700', 'primary' => '#FF8C00', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'gentlePink' => array('header' => '#FF69B4', 'footer' => '#FF69B4', 'secondary' => '#FFB6C1', 'primary' => '#FF1493', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'midnightTeal' => array('header' => '#008080', 'footer' => '#008080', 'secondary' => '#40E0D0', 'primary' => '#20B2AA', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'sunsetOrange' => array('header' => '#FF4500', 'footer' => '#FF4500', 'secondary' => '#FF6347', 'primary' => '#FF7F50', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'forestGreen' => array('header' => '#228B22', 'footer' => '#228B22', 'secondary' => '#32CD32', 'primary' => '#006400', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'icyBlue' => array('header' => '#00BFFF', 'footer' => '#00BFFF', 'secondary' => '#ADD8E6', 'primary' => '#1E90FF', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'rosyRed' => array('header' => '#FF6347', 'footer' => '#FF6347', 'secondary' => '#FF7F7F', 'primary' => '#FF4500', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'plumPurple' => array('header' => '#8B008B', 'footer' => '#8B008B', 'secondary' => '#DA70D6', 'primary' => '#9932CC', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'vibrantYellow' => array('header' => '#FFD700', 'footer' => '#FFD700', 'secondary' => '#FFFF00', 'primary' => '#FFA500', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'aquaMarine' => array('header' => '#7FFFD4', 'footer' => '#7FFFD4', 'secondary' => '#E0FFFF', 'primary' => '#40E0D0', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'burntSienna' => array('header' => '#E97451', 'footer' => '#E97451', 'secondary' => '#D2691E', 'primary' => '#CD5C5C', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'mintGreen' => array('header' => '#98FF98', 'footer' => '#98FF98', 'secondary' => '#ADFF2F', 'primary' => '#32CD32', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'sapphireBlue' => array('header' => '#0F52BA', 'footer' => '#0F52BA', 'secondary' => '#4682B4', 'primary' => '#1E90FF', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'coralPink' => array('header' => '#F88379', 'footer' => '#F88379', 'secondary' => '#FF7F50', 'primary' => '#FF6347', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'jadeGreen' => array('header' => '#00A36C', 'footer' => '#00A36C', 'secondary' => '#50C878', 'primary' => '#2E8B57', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'peachOrange' => array('header' => '#FFDAB9', 'footer' => '#FFDAB9', 'secondary' => '#FFE4B5', 'primary' => '#FFA07A', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'rubyRed' => array('header' => '#9B111E', 'footer' => '#9B111E', 'secondary' => '#FF6347', 'primary' => '#B22222', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'skyBlue' => array('header' => '#87CEEB', 'footer' => '#87CEEB', 'secondary' => '#B0E0E6', 'primary' => '#00BFFF', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'burntOrange' => array('header' => '#FF7F50', 'footer' => '#FF7F50', 'secondary' => '#FFA07A', 'primary' => '#FF4500', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'multicolor' => array('header' => '#cdb38b', 'footer' => '#eecfa1', 'secondary' => '#f5f5dc', 'primary' => '#4F4F4F', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2018_Palettes_A' => array('header' => '#a2b9bc', 'footer' => '#a2b9bc', 'secondary' => '#b2ad7f', 'primary' => '#878f99', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2018_Palettes_B' => array('header' => '#6b5b95', 'footer' => '#6b5b95', 'secondary' => '#feb236', 'primary' => '#d64161', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2017_Palettes_A' => array('header' => '#d6cbd3', 'footer' => '#d6cbd3', 'secondary' => '#eca1a6', 'primary' => '#bdcebe', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2017_Palettes_B' => array('header' => '#d5e1df', 'footer' => '#d5e1df', 'secondary' => '#e3eaa7', 'primary' => '#b5e7a0', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2017_Palettes_C' => array('header' => '#b9936c', 'footer' => '#b9936c', 'secondary' => '#dac292', 'primary' => '#e6e2d3', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2017_Palettes_D' => array('header' => '#3e4444', 'footer' => '#3e4444', 'secondary' => '#82b74b', 'primary' => '#405d27', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2016_Palettes_A' => array('header' => '#92a8d1', 'footer' => '#92a8d1', 'secondary' => '#034f84', 'primary' => '#f7cac9', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2016_Palettes_B' => array('header' => '#deeaee', 'footer' => '#deeaee', 'secondary' => '#b1cbbb', 'primary' => '#eea29a', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2016_Palettes_C' => array('header' => '#d5f4e6', 'footer' => '#d5f4e6', 'secondary' => '#80ced6', 'primary' => '#fefbd8', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2016_Palettes_D' => array('header' => '#ffef96', 'footer' => '#ffef96', 'secondary' => '#50394c', 'primary' => '#b2b2b2', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2016_Palettes_E' => array('header' => '#fefbd8', 'footer' => '#fefbd8', 'secondary' => '#618685', 'primary' => '#36486b', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2016_Palettes_F' => array('header' => '#b2b2b2', 'footer' => '#b2b2b2', 'secondary' => '#f4e1d2', 'primary' => '#f18973', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2015_Palettes_A' => array('header' => '#f0f0f0', 'footer' => '#f0f0f0', 'secondary' => '#c5d5c5', 'primary' => '#9fa9a3', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2015_Palettes_B' => array('header' => '#eaece5', 'footer' => '#eaece5', 'secondary' => '#b2c2bf', 'primary' => '#c0ded9', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2015_Palettes_C' => array('header' => '#e4d1d1', 'footer' => '#e4d1d1', 'secondary' => '#b9b0b0', 'primary' => '#d9ecd0', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    '2015_Palettes_D' => array('header' => '#f0efef', 'footer' => '#f0efef', 'secondary' => '#ddeedd', 'primary' => '#c2d4dd', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Rustic_Palettes_A' => array('header' => '#c8c3cc', 'footer' => '#c8c3cc', 'secondary' => '#563f46', 'primary' => '#8ca3a3', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Rustic_Palettes_B' => array('header' => '#e0e2e4', 'footer' => '#e0e2e4', 'secondary' => '#c6bcb6', 'primary' => '#96897f', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Rustic_Palettes_C' => array('header' => '#7e4a35', 'footer' => '#7e4a35', 'secondary' => '#cab577', 'primary' => '#dbceb0', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Rustic_Palettes_D' => array('header' => '#bbab9b', 'footer' => '#bbab9b', 'secondary' => '#8b6f47', 'primary' => '#d4ac6e', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Rustic_Palettes_E' => array('header' => '#686256', 'footer' => '#686256', 'secondary' => '#c1502e', 'primary' => '#587e76', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Rustic_Palettes_F' => array('header' => '#454140', 'footer' => '#454140', 'secondary' => '#bd5734', 'primary' => '#a79e84', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Sky_Palettes_A' => array('header' => '#bccad6', 'footer' => '#bccad6', 'secondary' => '#8d9db6', 'primary' => '#667292', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Sky_Palettes_B' => array('header' => '#cfe0e8', 'footer' => '#cfe0e8', 'secondary' => '#b7d7e8', 'primary' => '#87bdd8', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Sand_Palettes_A' => array('header' => '#fbefcc', 'footer' => '#fbefcc', 'secondary' => '#f9ccac', 'primary' => '#f4a688', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Sand_Palettes_B' => array('header' => '#fff2df', 'footer' => '#fff2df', 'secondary' => '#d9ad7c', 'primary' => '#a2836e', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Flower_Palettes_A' => array('header' => '#f9d5e5', 'footer' => '#f9d5e5', 'secondary' => '#eeac99', 'primary' => '#e06377', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Flower_Palettes_B' => array('header' => '#5b9aa0', 'footer' => '#5b9aa0', 'secondary' => '#d6d4e0', 'primary' => '#b8a9c9', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Beach_Palettes_A' => array('header' => '#96ceb4', 'footer' => '#96ceb4', 'secondary' => '#ffeead', 'primary' => '#ffcc5c', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Beach_Palettes_B' => array('header' => '#588c7e', 'footer' => '#588c7e', 'secondary' => '#f2e394', 'primary' => '#f2ae72', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Soft_Beach' => array('header' => '#51e2f5', 'footer' => '#51e2f5', 'secondary' => '#9df9ef', 'primary' => '#edf756', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Purple90' => array('header' => '#a0d2eb', 'footer' => '#a0d2eb', 'secondary' => '#e5eaf5', 'primary' => '#d0bdf4', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'GlobalPower' => array('header' => '#ff1d58', 'footer' => '#ff1d58', 'secondary' => '#f75990', 'primary' => '#fff685', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Rethink' => array('header' => '#f43a09', 'footer' => '#f43a09', 'secondary' => '#ffb766', 'primary' => '#c2edda', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Bankinggreen' => array('header' => '#beef00', 'footer' => '#beef00', 'secondary' => '#ff0028', 'primary' => '#657a00', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'Whitespace' => array('header' => '#fceed1', 'footer' => '#fceed1', 'secondary' => '#7d3cff', 'primary' => '#f2d53c', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'DeepblueSand' => array('header' => '#e1b382', 'footer' => '#e1b382', 'secondary' => '#c89666', 'primary' => '#2d545e', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'pink_and_red' => array('header' => '#fff5d7', 'footer' => '#fff5d7', 'secondary' => '#ff5e6c', 'primary' => '#feb300', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff'),
    'PinkGreen' => array('header' => '#9bc400', 'footer' => '#9bc400', 'secondary' => '#8076a3', 'primary' => '#f9c5bd', 'cardcolor' => '#000000', 'cardbackcolor' => '#ffffff')
);
// Die Farbschaltflächen habe ich eingefügt damit man sich ein gesamtbild der Farbschemas machen kann.
// Bitte last eure kereativität durch nichts stoppen ändert die colorSchemes wie es euch gefällt.
define('SHOW_COLOR_BUTTONS', false); // Farbschaltflächen anzeigen (true/false)
define('INITIAL_COLOR_SCHEME', 'Beach_Palettes_B'); // Farbschema auswählen

// Farben und Schriftart
// Setze aktuelle Farbschema basierend auf der Konstanten INITIAL_COLOR_SCHEME
$currentColorScheme = $colorSchemes[INITIAL_COLOR_SCHEME];

// definiere die Farben für Header, Footer und andere
define('HEADER_COLOR', $currentColorScheme['header']);   // Header-Farbe
define('FOOTER_COLOR', $currentColorScheme['footer']);   // Footer-Farbe
define('SECONDARY_COLOR', $currentColorScheme['secondary']);  // Sekundärfarbe
define('PRIMARY_COLOR', $currentColorScheme['primary']); // Primäre Schriftfarbe
define('CARD_COLOR', $currentColorScheme['cardcolor']); // Card Schriftfarbe
define('CARD_BACKGRUND_COLOR', $currentColorScheme['cardbackcolor']); // Card Hintergrundfarbe

define('FONT_FAMILY_STATS', 'Arial, Verdana, sans-serif');
define('FONT_FAMILY', 'Pacifico, normal, serif');

// Schriftgrößen
define('BASE_FONT_SIZE', '26px'); // Standardgröße für Text
define('TITLE_FONT_SIZE', '48px'); // Größe für Titel
define('HEADLINE_FONT_SIZE', '30px'); // Größe für Überschriften
define('STATS_FONT_SIZE', '14px'); // Größe für Statistik-Text
define('CARD_FONT_SIZE', '18px'); // Größe für Cardk-Text
define('CARD_BORDER_RADIUS', '15px'); // Rundet die Ecken der card ab

// Links
define('LINK_COLOR', '#3A3A3A'); // Standard Link-Farbe
define('LINK_HOVER_COLOR', 'red'); // Link-Farbe beim Hover

// Hintergrund- und Vordergrundbilder
define('BACKGROUND_IMAGE', 'pics/Pastelltueren.jpg'); // Hintergrundbild
define('FOREGROUND_IMAGE', 'pics/transparent.png'); // Logo oder Vordergrundbild
define('BACKGROUND_OPACITY', 1.0); // Transparenz des Hintergrunds
define('FOREGROUND_OPACITY', 1.0); // Transparenz des Logos

// Anzeigeoptionen
define('LOGO_ON', 'OFF'); // Logo anzeigen: ON / OFF
define('TEXT_ON', 'ON'); // Begrüßungstext anzeigen: ON / OFF
define('LOGO_PATH', 'include/Metavers150.png'); // Pfad zum Logo
define('LOGO_WIDTH', '50%'); // Logo-Breite
define('LOGO_HEIGHT', '25%'); // Logo-Höhe
define('GUIDE_DATA', 'DATA'); // DATA/JSON guide anzeigen.

// Begrüßungstext
define('PRIMARY_COLOR_LOGO', '#FFFFFF'); // Allgemeine Schriftfarbe Schwarz
define('WELCOME_TEXT', '<p> &nbsp; Willkommen im ' . SITE_NAME . '</p>');
define('WELCOME_TEXT_WIDTH', '50%');  // Standardbreite (z. B. 50%)
define('WELCOME_TEXT_HEIGHT', 'auto');  // Standardhöhe (auto für flexible Höhe)
define('WELCOME_TEXT_COLOR', PRIMARY_COLOR);  // Farbe des Textes
define('WELCOME_TEXT_ALIGN', 'left');  // Zentriert, links oder rechts
define('WELCOME_TEXT_FONT_SIZE', '24px');  // Schriftgröße des Textes

// Bildanzeige-Einstellungen
define('SLIDESHOW_FOLDER', './images'); // Verzeichnis für die Bilder
define('IMAGE_SIZE', 'width:100%;height:100%'); // Größe der Bilder (100% für Vollbild)
define('SLIDESHOW_DELAY', 9000); // Zeit zwischen Bildern (in ms, 9000 = 9 Sekunden)

// Einstellungen für Maptiles
define('FREI_COLOR', '#0088FF'); // Farbe für freie Koordinaten
define('BESCHLAGT_COLOR', '#55C155'); // Farbe für SingleRegion
define('VARREGION_COLOR', '#006400'); // Farbe für VarRegion
define('CENTER_COLOR', '#FF0000'); // Farbe für Zentrum
define('TILE_SIZE', '25px'); // Größe der Farbfelder

// Zentrum des Grids
define('CONF_CENTER_COORD_X', 5500); // X-KOORDINATE DES ZENTRUMS
define('CONF_CENTER_COORD_Y', 5500); // Y-KOORDINATE DES ZENTRUMS

define('MAPS_X', 32);
define('MAPS_Y', 32);

// MOTD-Einstellung: 'Dyn' für dynamisch, 'Static' für statisch
define('MOTD', 'Dyn'); // Oder 'Static'

// Statische MOTD (nur relevant, wenn MOTD auf 'Static' gesetzt ist)
define('MOTD_STATIC_MESSAGE', 'Willkommen auf im Grid! Bitte beachte unsere Regeln.');
define('MOTD_STATIC_TYPE', 'system');
define('MOTD_STATIC_URL_TOS', BASE_URL . '/include/tos.php');
define('MOTD_STATIC_URL_DMCA', BASE_URL . '/include/dmca.php');

// Definiere verschiedene RSS-Feed-URLs getrennt durch Komma.
$feed_urls = [
    'http://opensimulator.org/viewgit/?a=rss-log&p=opensim', // Standard-Feed
    'https://www.hypergridbusiness.com/feed'
];

// Maximale Anzahl der Einträge pro Feed
$max_entries = 50;
?>
