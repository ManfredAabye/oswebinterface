<?php
session_start(); // PHP-Session starten

$title = "New Password";
include_once 'include/header.php';

// Funktion zur Erstellung eines Salts
function ospswdsalt() {
    return md5(uniqid(mt_rand(), true));
}

// Funktion zur Erstellung des Passwort-Hashes
function ospswdhash($osPasswd, $osSalt) {
    return md5(md5($osPasswd) . ":" . $osSalt);
}

// include/verification_functions.php

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

$showCard1 = true; // Standardmäßig Card1 anzeigen
$showCard2 = false; // Card2 standardmäßig ausblenden

// Überprüfen, ob das Formular abgesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["generateCode"])) {
        // Verifizierungscode generieren und per E-Mail senden
        $osVorname = trim($_POST["osVorname"]);
        $osNachname = trim($_POST["osNachname"]);

        // Werte in der Session speichern
        $_SESSION['osVorname'] = $osVorname;
        $_SESSION['osNachname'] = $osNachname;

        try {
            // Datenbankverbindung herstellen
            $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Benutzer anhand von Vorname und Nachname abrufen
            $statement = $pdo->prepare("SELECT PrincipalID, Email FROM UserAccounts WHERE FirstName = :FirstName AND LastName = :LastName");
            $statement->execute(['FirstName' => $osVorname, 'LastName' => $osNachname]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo "Benutzer nicht gefunden.";
                exit;
            }

            $email = $user['Email'];
            $principalID = $user['PrincipalID'];

            // Verifizierungscode generieren (als Variable, nicht in der Datenbank speichern)
            $activationCode = generateActivationCode();

            // E-Mail an den Benutzer senden
            $subject = "Ihr Freischaltcode für " . SITE_NAME;
            $message = "Hallo $osVorname $osNachname,\n\n";
            $message .= "Ihr Freischaltcode lautet: $activationCode\n\n";
            $message .= "Bitte verwenden Sie diesen Code, um Ihr Passwort auf " . BASE_URL . " zu ändern.\n\n";
            $message .= "Mit freundlichen Grüßen,\n";
            $message .= SITE_NAME;

            $headers = "From: noreply@" . parse_url(BASE_URL, PHP_URL_HOST) . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($email, $subject, $message, $headers)) {
                echo "Die E-Mail wurde an $email gesendet. Bitte schauen Sie in Ihren E-Mail-Account.";
                $showCard1 = false; // Card1 ausblenden
                $showCard2 = true;  // Card2 anzeigen
            } else {
                echo "Fehler beim Senden der E-Mail.";
            }

        } catch (PDOException $e) {
            echo "Fehler: " . $e->getMessage();
        }
    } else {
        // Passwort ändern
        $osVorname = $_SESSION['osVorname']; // Werte aus der Session abrufen
        $osNachname = $_SESSION['osNachname'];
        $activationCode = trim($_POST["activationCode"]);
        $newPassword = trim($_POST["newPassword"]);

        if (empty($osVorname) || empty($osNachname) || empty($activationCode) || empty($newPassword)) {
            echo "Bitte füllen Sie alle Felder aus.";
            exit;
        }

        try {
            // Datenbankverbindung herstellen
            $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Benutzer anhand des Vor- und Nachnamens suchen
            $statement = $pdo->prepare("SELECT PrincipalID FROM UserAccounts WHERE FirstName = :FirstName AND LastName = :LastName");
            $statement->execute(['FirstName' => $osVorname, 'LastName' => $osNachname]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo "Benutzer nicht gefunden.";
                exit;
            }

            $principalID = $user['PrincipalID'];

            // Generiere ein neues Salt und Hash für das neue Passwort
            $newSalt = ospswdsalt(); // Funktion aufrufen
            $newHash = ospswdhash($newPassword, $newSalt); // Funktion aufrufen

            // Passwort in der Tabelle `auth` aktualisieren
            $updateStatement = $pdo->prepare("UPDATE auth SET passwordHash = :passwordHash, passwordSalt = :passwordSalt WHERE UUID = :UUID");
            $updateStatement->execute([
                'passwordHash' => $newHash,
                'passwordSalt' => $newSalt,
                'UUID' => $principalID
            ]);

            echo "Passwort erfolgreich geändert.";

        } catch (PDOException $e) {
            echo "Fehler: " . $e->getMessage();
        }

        // Datenbankverbindung schließen
        $pdo = null;
    }
}
?>

<html>
<head>
    <meta charset="utf-8">
    <title>Passwort ändern</title>
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
    <!-- Card1 zuerst anzeigen -->
    <div class="card1" style="display: <?php echo $showCard1 ? 'block' : 'none'; ?>;">
        <h2>New Password verify</h2> 
        <form action="" method="post">
            <label for="osVorname">Vorname:</label>
            <input type="text" name="osVorname" required><br>
            <label for="osNachname">Nachname:</label>
            <input type="text" name="osNachname" required><br>
            <input type="submit" name="generateCode" value="Freischaltcode senden"><br><br>
        </form>
    </div>

    <!-- Card2 nach dem Senden des Freischaltcodes anzeigen -->
    <div class="card2" style="display: <?php echo $showCard2 ? 'block' : 'none'; ?>;">
        <h2>New Password now</h2>
        <form action="" method="post">
            <label for="activationCode">Freischaltcode aus der gesendeten E-Mail:</label>
            <input type="text" name="activationCode" required><br>
            <label for="newPassword">Neues Passwort:</label>
            <input type="password" name="newPassword" required><br>
            <input type="submit" value="Passwort ändern">
        </form>
    </div>
</main>
</body>
</html>
