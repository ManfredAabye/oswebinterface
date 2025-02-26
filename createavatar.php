<?php
session_start(); // PHP-Session starten

$title = "Register";
include_once 'include/header.php';

// Liste bekannter Wegwerf-Domains
$disposable_domains = [
    'maildrop.cc',
    'discard.email',
    'fakeinbox.org',
    'disposableemailaddresses:email.co.uk',
    'temp-mail.ru',
    'mytrashmail.com',
    'getairmail.net',
    'trash-mail.de',
    'trashmail.me',
    'mail-temporaire.fr',
    'nada.email',
    'tempinbox.xyz',
    'spambog.com',
    'spambox.us',
    'anonbox.net',
    'mail.kz',
    'temp-mail.org',
    'luxusmail.ru',
    // Weitere Domains hinzufügen
];

// Liste unerwünschter TLDs
$blocked_tlds = ['com', 'cn', 'ru', 'pl'];

// Funktion zur Überprüfung, ob eine E-Mail-Adresse von einer Wegwerf-Domain oder unerwünschten TLD stammt
function is_disposable_email($email, $disposable_domains, $blocked_tlds) {
    $domain = substr(strrchr($email, "@"), 1);
    $tld = substr(strrchr($domain, "."), 1);
    return in_array($domain, $disposable_domains) || in_array($tld, $blocked_tlds);
}

// Funktionen direkt in der Datei definieren
function generateActivationCode() {
    return bin2hex(random_bytes(16));
}

function sendVerificationEmail($email, $vorname, $nachname, $activationCode) {
    $subject = "Ihr Freischaltcode für " . SITE_NAME;
    $message = "Hallo $vorname $nachname,\n\n";
    $message .= "Ihr Freischaltcode lautet: $activationCode\n\n";
    $message .= "Bitte verwenden Sie diesen Code, um Ihre Registrierung abzuschließen.\n\n";
    $message .= "Mit freundlichen Grüßen,\n";
    $message .= SITE_NAME;

    $headers = "From: noreply@" . parse_url(BASE_URL, PHP_URL_HOST) . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    return mail($email, $subject, $message, $headers);
}

function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Salt erstellen
function ospswdsalt() {
    global $benutzeruuid;
    $randomuuid = $benutzeruuid;
    $strrep = str_replace("-", "", $randomuuid);
    return md5($strrep);
}

// Md5Hash(password) + ":" + passwordSalt
function ospswdhash($osPasswd, $osSalt) {
    return md5(md5($osPasswd) . ":" . $osSalt);
}

// Überprüfen, ob das Formular abgesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_card1'])) {
        // Daten aus Card1 speichern
        $_SESSION['osVorname'] = trim($_POST['osVorname']);
        $_SESSION['osNachname'] = trim($_POST['osNachname']);
        $_SESSION['osEMail'] = trim($_POST['osEMail']);
        $_SESSION['osPasswd'] = trim($_POST['osPasswd']);
        $_SESSION['osPasswd1'] = trim($_POST['osPasswd1']);

        // Validierungen für Card1
        if (empty($_SESSION['osVorname']) || empty($_SESSION['osNachname']) || empty($_SESSION['osEMail']) || empty($_SESSION['osPasswd']) || empty($_SESSION['osPasswd1'])) {
            echo "Bitte füllen Sie alle Felder aus.";
            exit;
        }
        if ($_SESSION['osPasswd'] != $_SESSION['osPasswd1']) {
            echo "Die Passwörter müssen übereinstimmen.";
            exit;
        }
        if (is_disposable_email($_SESSION['osEMail'], $disposable_domains, $blocked_tlds)) {
            echo "Bitte verwenden Sie keine Wegwerf-E-Mail-Adresse oder eine E-Mail-Adresse mit einer gesperrten Domain.";
            exit;
        }

        // Freischaltcode generieren und per E-Mail senden
        $activationCode = generateActivationCode();
        $_SESSION['activationCode'] = $activationCode; // Freischaltcode in der Session speichern

        if (!sendVerificationEmail($_SESSION['osEMail'], $_SESSION['osVorname'], $_SESSION['osNachname'], $activationCode)) {
            echo "Fehler beim Senden der E-Mail.";
            exit;
        }

        // Card1 ausblenden und Card2 anzeigen
        $showCard1 = false;
        $showCard2 = true;
    } elseif (isset($_POST['submit_card2'])) {
        // Überprüfen des Freischaltcodes
        $enteredCode = trim($_POST['activationCode']);

        if ($enteredCode === $_SESSION['activationCode']) {
            // Freischaltcode ist korrekt, fahre mit der Registrierung fort

            // Variablen aus der Session holen
            $osVorname = $_SESSION['osVorname'];
            $osNachname = $_SESSION['osNachname'];
            $osEMail = $_SESSION['osEMail'];
            $osPasswd = $_SESSION['osPasswd'];

            // UUIDs generieren
            $benutzeruuid = generateUUID();
            $inventoryuuid = generateUUID();
            $neuparentFolderID = generateUUID();
            $neuHauptFolderID = generateUUID();

            // Salt und Hash generieren
            $osSalt = ospswdsalt();
            $osHash = ospswdhash($osPasswd, $osSalt);

            // Zeitstempel erstellen
            $osDatum = time();

            // Datenbankverbindung herstellen
            $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Datenbankzugriffe in createavatarfunc.php durchführen
            include_once("include/createavatarfunc.php");

            // Erfolgsmeldung anzeigen
            echo "Registrierung erfolgreich abgeschlossen!";
        } else {
            echo "Ungültiger Freischaltcode. Bitte versuchen Sie es erneut.";
        }
    }
} else {
    // Standardmäßig Card1 anzeigen
    $showCard1 = true;
    $showCard2 = false;
}
?>

<html>
<head>
    <meta charset="utf-8">
    <title>Register</title>
    <style>
        htmlBody {font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;} 
        .card1, .card2 {
            width: 50%; 
            margin: 2em auto; 
            padding: 2em; 
            background-color: #ffffff; 
            border: 1px solid #ccc; 
            border-radius: 15px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {color: #333;} 
        form label {display: block; margin-bottom: 0.5em; color: #333;} 
        form input[type="text"], form input[type="password"], form input[type="email"] {
            width: 100%; 
            padding: 0.5em; 
            margin-bottom: 1em; 
            border: 1px solid #ccc; 
            border-radius: 4px;
        } 
        form input[type="submit"] {
            padding: 0.7em 2em; 
            background-color: #007BFF; 
            color: #ffffff; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer;
        } 
        form input[type="submit"]:hover {background-color: #0056b3;}
    </style>
</head>
<body>

<main>
    <!-- Card1: Eingabe von Vorname, Nachname, E-Mail, Passwort und Passwortwiederholung -->
    <div class="card1" style="display: <?php echo $showCard1 ? 'block' : 'none'; ?>;">
        <h2>Registrierung - Schritt 1</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="osVorname">Vorname:</label>
                <input type="text" name="osVorname" placeholder="John" maxlength="40" required />
            </div>
            <div class="form-group">
                <label for="osNachname">Nachname:</label>
                <input type="text" name="osNachname" placeholder="Doe" maxlength="40" required />
            </div>
            <div class="form-group">
                <label for="osEMail">E-Mail:</label>
                <input type="email" name="osEMail" placeholder="john@doe.com" maxlength="40" required />
            </div>
            <div class="form-group">
                <label for="osPasswd">Passwort:</label>
                <input type="password" name="osPasswd" placeholder="*********" maxlength="40" required />
            </div>
            <div class="form-group">
                <label for="osPasswd1">Passwort wiederholen:</label>
                <input type="password" name="osPasswd1" placeholder="*********" maxlength="40" required />
            </div>
            <div class="form-group">
                <input type="submit" name="submit_card1" value="Senden">
            </div>
        </form>
    </div>

    <!-- Card2: Eingabe des Freischaltcodes -->
    <div class="card2" style="display: <?php echo $showCard2 ? 'block' : 'none'; ?>;">
        <h2>Registrierung - Schritt 2</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="activationCode">Freischaltcode aus der E-Mail:</label>
                <input type="text" name="activationCode" placeholder="Freischaltcode eingeben" maxlength="36" required />
            </div>
            <div class="form-group">
                <input type="submit" name="submit_card2" value="Registrieren">
            </div>
        </form>
    </div>
</main>

</body>
</html>
