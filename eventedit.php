
<?php
// Passwortschutz und Session-Start Datei: eventedit.php
// Dieser Bereich schützt den Event-Editor mit einem Passwort und startet die Session.
session_start();

$title = "Event Editor Service";
include_once 'include/header.php';


// Authentifizierung: Passwort prüfen und Session setzen
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_password = $_POST['password'] ?? '';
    // Überprüfen des Passworts
    if (in_array($input_password, $registration_passwords_events)) {
        $_SESSION['authenticated'] = true;
    } else {
        $error_message = "Falsches Passwort. Bitte versuchen Sie es erneut.";
    }
}


// Logout-Funktion: Session beenden und zurück zum Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: eventedit.php");
    exit;
}


// Login-Formular anzeigen, wenn nicht authentifiziert
if (!isset($_SESSION['authenticated'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Calendar Editor</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: auto; }
        h2 { text-align: center; }
        .input-group { margin-bottom: 15px; }
        input[type="password"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h2>Events Calendar Editor</h2>
    <form method="POST">
        <div class="input-group">
            <input type="password" name="password" placeholder="Passwort eingeben" required />
        </div>
        <button type="submit">Login</button>
    </form>
</body>
</html>
<?php
    exit;
}



// Funktion zum Speichern der Events in einer JSON-Datei mit wählbarem Dateinamen


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['events'])) {
    $events = json_decode($_POST['events'], true);
    $filename = $_POST['filename'] ?? 'events.json';
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename); // Sicherheit: nur erlaubte Zeichen
    $filepath = 'calendar/' . $filename;
    $backupFilename = $filename . '_' . date('Y-m-d_H-i-s') . '.bak.json';

    // Events nach Datum sortieren (aufsteigend)
    usort($events, function($a, $b) {
        return strcmp($a['date'], $b['date']);
    });

    // Datei anlegen, falls sie nicht existiert
    if (!file_exists($filepath)) {
        file_put_contents($filepath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "<script>alert('Datei wurde neu angelegt und Events gespeichert!');</script>";
    } else {
        copy($filepath, 'calendar/' . $backupFilename);
        echo "<script>alert('Events erfolgreich gespeichert!');</script>";
    }
    // Events speichern
    if (file_put_contents($filepath, json_encode($events, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
        echo "<script>alert('Fehler beim Speichern der Datei!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Editor</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: auto; }
        h1 { text-align: center; }
        .input-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="date"], input[type="color"], textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .event-list { margin-top: 20px; }
        .event-item { border: 1px solid #ccc; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .event-item h3 { margin: 0 0 10px; }
        form.logout { display: flex; justify-content: center; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Events Editor</h1>

    <!-- Event-Editor UI: Eingabefelder und Event-Liste -->
    <div id="editor">
        <div class="input-group">
            <label for="event-date">Datum:</label>
            <input type="date" id="event-date" />
        </div>
        <div class="input-group">
            <label for="event-image">Bild URL:</label>
            <input type="text" id="event-image" />
        </div>
        <div class="input-group">
            <label for="event-texts">Texte (kommagetrennt):</label>
            <textarea id="event-texts"></textarea>
        </div>
        <div class="input-group">
            <label for="event-link">Link:</label>
            <input type="text" id="event-link" />
        </div>

        <div class="input-group">
            <label for="event-color">Hintergrundfarbe:</label>
            <input type="color" id="event-color" value="#000000" />
        </div>
        <div class="input-group">
            <label for="event-txtcolor">Schriftfarbe:</label>
            <input type="color" id="event-txtcolor" value="#ffffff" />
        </div>

        <div class="input-group">
            <label for="event-filename">Dateiname (z.B. events2026.json):</label>
            <input type="text" id="event-filename" value="events.json" />
        </div>
        <button type="button" onclick="addEvent()">Event hinzufügen</button>
        <button type="button" onclick="downloadEvents()">Download</button>
        <button type="button" onclick="saveEvents()">Speichern</button>

        <div class="event-list" id="event-list"></div>

        <form method="POST" class="logout">
            <button type="submit" name="logout">Session beenden</button>
        </form>
    </div>

    <script>
        // JavaScript: Event-Verwaltung und UI-Funktionen
        // events-Array hält alle geladenen und hinzugefügten Events
        let events = [];

        // Events aus der JSON-Datei laden und anzeigen (Dateiname wählbar)
        async function fetchEvents() {
            const filename = document.getElementById('event-filename').value || 'events.json';
            try {
                const response = await fetch('calendar/' + filename);
                events = await response.json();
            } catch (e) {
                events = [];
            }
            displayEvents();
        }

        // Events in der UI anzeigen
        function displayEvents() {
            const eventList = document.getElementById('event-list');
            eventList.innerHTML = '';
            events.forEach((event, index) => {
                const eventItem = document.createElement('div');
                eventItem.classList.add('event-item');
                eventItem.innerHTML = `
                    <h3>${event.date}</h3>
                    <p><strong>Bild URL:</strong> ${event.image}</p>
                    <p><strong>Texte:</strong> ${event.texts.join(', ')}</p>
                    <p><strong>Link:</strong> <a href="${event.link}" target="_blank">${event.link}</a></p>
                    <p><strong>Hintergrundfarbe:</strong> <span style="background-color:${event.color}; padding: 2px 5px;">${event.color}</span></p>
                    <p><strong>Schriftfarbe:</strong> <span style="background-color:${event.txtcolor}; padding: 2px 5px;">${event.txtcolor}</span></p>
                    <button onclick="deleteEvent(${index})">Löschen</button>
                `;
                eventList.appendChild(eventItem);
            });
        }

        // Neues Event aus den Eingabefeldern hinzufügen
        function addEvent() {
            const date = document.getElementById('event-date').value;
            const image = document.getElementById('event-image').value;
            const texts = document.getElementById('event-texts').value.split(',');
            const link = document.getElementById('event-link').value;
            const color = document.getElementById('event-color').value;
            const txtcolor = document.getElementById('event-txtcolor').value;

            if (date && texts.length > 0 && link) {
                // Prüfen, ob ein Event mit dem gleichen Datum existiert
                const index = events.findIndex(event => event.date === date);
                const newEvent = { date, image, texts, link, color, txtcolor };
                if (index !== -1) {
                    // Event ersetzen
                    events[index] = newEvent;
                } else {
                    // Neues Event anhängen
                    events.push(newEvent);
                }
                // Nach Datum sortieren (aufsteigend)
                events.sort((a, b) => a.date.localeCompare(b.date));
                displayEvents();
            } else {
                alert('Bitte alle Felder ausfüllen.');
            }
        }

        // Event aus der Liste löschen
        function deleteEvent(index) {
            events.splice(index, 1);
            displayEvents();
        }

        // Hilfsfunktion: JSON formatieren
        function formatJSON(json) {
            return JSON.stringify(json, null, 2);
        }

        // Events als Datei herunterladen

        // Events als Datei herunterladen (Dateiname wählbar)
        function downloadEvents() {
            const filename = document.getElementById('event-filename').value || 'events.json';
            const formattedEvents = formatJSON(events);
            const blob = new Blob([formattedEvents], { type: 'application/json' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'calendar/' + filename;
            link.click();
        }

        // Events an den Server senden und speichern (optional)

        // Events an den Server senden und speichern (Dateiname wählbar)
        async function saveEvents() {
            const filename = document.getElementById('event-filename').value || 'events.json';
            const formattedEvents = formatJSON(events);
            const formData = new FormData();
            formData.append('events', formattedEvents);
            formData.append('filename', filename);

            const response = await fetch('eventedit.php', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                alert('Events erfolgreich gespeichert!');
            } else {
                alert('Fehler beim Speichern.');
            }
        }

        // Nach dem Laden der Seite Events laden
        document.addEventListener('DOMContentLoaded', fetchEvents);
        document.getElementById('event-filename').addEventListener('change', fetchEvents);
    </script>
</body>
</html>
