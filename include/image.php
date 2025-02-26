<?php
// Konfiguration einbinden
include_once "include/config.php";

// Überprüfen, ob der Parameter 'image_url' übergeben wurde
$image_url = isset($_GET['image_url']) ? $_GET['image_url'] : '';

// Sicherstellen, dass eine URL vorhanden ist
if (empty($image_url)) {
    die('Kein Bild-URL angegeben!');
}

// Bild aus der URL holen
$image_data = file_get_contents($image_url);

// Wenn das Bild nicht geladen werden konnte
if ($image_data === false) {
    die('Fehler beim Laden des Bildes!');
}

// Mit Imagick das Bild in das gewünschte Format konvertieren
try {
    $imagick = new Imagick();
    $imagick->readImageBlob($image_data);

    // Prüfen, ob es ein JP2-Bild ist und konvertiere es zu JPG
    if ($imagick->getImageFormat() == 'JPEG2000') {
        $imagick->setImageFormat('jpg'); // Konvertierung zu JPG
    }

    // Bild an den Browser ausgeben
    header('Content-Type: image/jpeg'); // JPG-MIME-Typ für JPG-Bilder
    echo $imagick->getImageBlob();
} catch (Exception $e) {
    die('Fehler beim Konvertieren des Bildes: ' . $e->getMessage());
}
?>
