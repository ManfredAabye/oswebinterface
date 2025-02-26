<?php
// Einbinden der Konfigurationsdatei
require_once __DIR__ . '/include/config.php';

// Header setzen, um den Inhaltstyp auf JSON festzulegen
header('Content-Type: application/json');

// Nachrichtendaten basierend auf der MOTD-Einstellung erstellen
if (MOTD === 'Dyn') {
    // Dynamische MOTD
    $hour = date('H');
    if ($hour < 12) {
        $greeting = "Guten Morgen";
    } else {
        $greeting = "Guten Tag";
    }
    $message = [
        "message" => "$greeting auf " . SITE_NAME . "! Bitte beachte unsere Regeln und Richtlinien.",
        "type" => "system",
        "url_tos" => BASE_URL . "/include/tos.php",
        "url_dmca" => BASE_URL . "/include/dmca.php"
    ];
} else {
    // Statische MOTD
    $message = [
        "message" => MOTD_STATIC_MESSAGE,
        "type" => MOTD_STATIC_TYPE,
        "url_tos" => MOTD_STATIC_URL_TOS,
        "url_dmca" => MOTD_STATIC_URL_DMCA
    ];
}

// JSON-Ausgabe
echo json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>