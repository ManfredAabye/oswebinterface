<?php
session_start();

$title = "AvatarPicker Service";
include_once 'include/header.php';


// Fehlerberichterstattung aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verbindung zur Datenbank herstellen
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Überprüfen, ob die Verbindung erfolgreich war
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$vorname = $nachname = '';
$inventory = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_password = $_POST['input_password'] ?? '';
    $vorname = $_POST['vorname'] ?? '';
    $nachname = $_POST['nachname'] ?? '';

    // Überprüfen des Passworts
    if (in_array($input_password, $registration_passwords_avatarpicker)) {
        $_SESSION['authenticated'] = true;

        if (!empty($vorname) && !empty($nachname)) {
            $inventory = listinventar($conn, $vorname, $nachname);
        }
    } else {
        $error_message = "Falsches Passwort. Bitte versuchen Sie es erneut.";
    }
} else {
    // Benutzer ist nicht authentifiziert
    $show_login_form = true;
}

// Funktion zum Abrufen des Inventars
function listinventar($conn, $vorname, $nachname) {
    $vorname = $conn->real_escape_string(strip_tags($vorname));
    $nachname = $conn->real_escape_string(strip_tags($nachname));
    $query = "SELECT PrincipalID FROM UserAccounts WHERE FirstName='$vorname' AND LastName='$nachname'";
    $result = $conn->query($query);
    
    if ($result->num_rows == 0) {
        echo "Benutzer nicht gefunden.\n";
        return [];
    }
    
    $row = $result->fetch_assoc();
    $user_uuid = $row['PrincipalID'];

    // Abfrage der "Outfits"-Ordner
    $query = "SELECT folderID, folderName FROM inventoryfolders WHERE agentID='$user_uuid' AND type=47 ORDER BY folderName ASC, agentID ASC";
    $result = $conn->query($query);
    $inventory = [];
    
    while ($row = $result->fetch_assoc()) {
        $inventory[] = [
            'folderID' => $row['folderID'],
            'folderName' => $row['folderName']
        ];
    }

    if (empty($inventory)) {
        echo "Keine Outfits gefunden.\n";
    }

    return $inventory;
}

// Funktion zum Abrufen des Bildes nach Namen
function getImageByName($dir, $name) {
    foreach (glob($dir."*.jpg") as $filename) {
        $file = pathinfo($filename, PATHINFO_FILENAME);
        if ($file === $name) {
            return $dir.$file.".jpg";
        }
    }
    return $dir."default.jpg";
}
?>

<style>
    body { font-family: Arial, sans-serif; color: black; margin: 0; padding: 0; display: flex; flex-direction: column; height: 100vh; }
    .containerlist { display: flex; flex-direction: column; flex: 1; }
    header, footer { flex-shrink: 0; }
    main { flex-grow: 1; display: flex; justify-content: center; align-items: center; }
    .form-containerlist { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); max-width: 600px; width: 100%; }
    h1 { font-size: 24px; margin-bottom: 20px; text-align: center; }
    label { display: block; margin-bottom: 8px; font-weight: bold; }
    input[type="text"], input[type="password"] { width: calc(100% - 20px); padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
    input[type="submit"] { width: 100%; padding: 10px; border: none; border-radius: 4px; background-color: #007bff; color: #fff; font-size: 16px; cursor: pointer; margin-bottom: 10px; }
    input[type="submit"]:hover { background-color: #0056b3; }
    .inventory-list { list-style-type: none; padding: 0; }
    .inventory-list li { padding: 8px; background-color: #f9f9f9; margin-bottom: 5px; border: 1px solid #ccc; border-radius: 4px; }
    .outfit-container { margin: 10px; }
    .img-thumbnail { max-width: 100px; }
</style>

<div class="containerlist">
    <main>
        <div class="form-containerlist">
            <h1>AvatarPicker</h1>
            <form method="POST" action="">
                <label for="vorname">Vorname:</label>
                <input type="text" id="vorname" name="vorname" required><br>
                
                <label for="nachname">Nachname:</label>
                <input type="text" id="nachname" name="nachname" required><br>
                
                <label for="input_password">Passwort:</label>
                <input type="password" id="input_password" name="input_password" required><br>
                
                <input type="submit" value="Outfits anzeigen">
            </form>

            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>

            <?php if ($_SESSION['authenticated'] ?? false && !empty($inventory)): ?>
                <h2>Outfits von <?= htmlspecialchars($vorname) ?> <?= htmlspecialchars($nachname) ?></h2>
                <div class="inventory-list">
                    <?php foreach ($inventory as $item): ?>
                        <div class="outfit-container">
                            <a href="secondlife:///app/wear_folder/?folder_id=<?= htmlspecialchars($item['folderID']) ?>" target="_self">
                                <img class="img-thumbnail" src="<?= htmlspecialchars(getImageByName("pics/", $item['folderName'])) ?>" alt="<?= htmlspecialchars($item['folderName']) ?>">
                                <p><?= htmlspecialchars($item['folderName']) ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
