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
    $eventsByDate = json_decode($_POST['events'], true);
    $filename = $_POST['filename'] ?? 'events.json';
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename); // Sicherheit: nur erlaubte Zeichen
    $filepath = 'calendar/' . $filename;
    $backupFilename = $filename . '_' . date('Y-m-d_H-i-s') . '.bak.json';

    // EventsByDate nach Datum sortieren (aufsteigend)
    usort($eventsByDate, function($a, $b) {
        return strcmp($a['date'], $b['date']);
    });
    // Innerhalb jedes Tages die Events nach Zeit sortieren
    foreach ($eventsByDate as &$day) {
        if (isset($day['events']) && is_array($day['events'])) {
            usort($day['events'], function($a, $b) {
                return strcmp($a['time'] ?? '', $b['time'] ?? '');
            });
        }
    }
    unset($day);

    // Datei anlegen, falls sie nicht existiert
    if (!file_exists($filepath)) {
        file_put_contents($filepath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "<script>alert('Datei wurde neu angelegt und Events gespeichert!');</script>";
    } else {
        copy($filepath, 'calendar/' . $backupFilename);
        echo "<script>alert('Events erfolgreich gespeichert!');</script>";
    }
    // Events speichern
    if (file_put_contents($filepath, json_encode($eventsByDate, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
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
            <label for="event-time">Uhrzeit (z.B. 18:00):</label>
            <input type="time" id="event-time" />
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
        // JavaScript: Event-Verwaltung und UI-Funktionen für verschachtelte Events
        // eventsByDate ist ein Array von {date, events: [ ... ]}
        let eventsByDate = [];

        // Events aus der JSON-Datei laden und anzeigen (Dateiname wählbar)
        async function fetchEvents() {
            const filename = document.getElementById('event-filename').value || 'events.json';
            try {
                const response = await fetch('calendar/' + filename);
                const data = await response.json();
                // Prüfen, ob verschachteltes Format (date, events)
                if (Array.isArray(data) && data.length > 0 && data[0].events) {
                    eventsByDate = data;
                } else {
                    // Migration: Falls altes Format, umwandeln
                    eventsByDate = [];
                    data.forEach(ev => {
                        let found = eventsByDate.find(e => e.date === ev.date);
                        if (!found) {
                            found = { date: ev.date, events: [] };
                            eventsByDate.push(found);
                        }
                        found.events.push({
                            time: ev.time || '',
                            image: ev.image,
                            texts: ev.texts,
                            link: ev.link,
                            color: ev.color,
                            txtcolor: ev.txtcolor
                        });
                    });
                }
            } catch (e) {
                eventsByDate = [];
            }
            displayEvents();
        }

        // Events in der UI anzeigen
        function displayEvents() {
            const eventList = document.getElementById('event-list');
            eventList.innerHTML = '';
            eventsByDate.sort((a, b) => a.date.localeCompare(b.date));
            eventsByDate.forEach((day, dayIdx) => {
                const dayDiv = document.createElement('div');
                dayDiv.classList.add('event-item');
                let html = `<h3>${day.date}</h3>`;
                day.events.sort((a, b) => (a.time || '').localeCompare(b.time || ''));
                day.events.forEach((ev, evIdx) => {
                    html += `<div style="margin-left:1em; border-left:2px solid #ccc; padding-left:0.5em; margin-bottom:0.5em;">
                        <strong>Zeit:</strong> ${ev.time || ''}<br>
                        <strong>Bild URL:</strong> ${ev.image}<br>
                        <strong>Texte:</strong> ${(ev.texts||[]).join(', ')}<br>
                        <strong>Link:</strong> <a href="${ev.link}" target="_blank">${ev.link}</a><br>
                        <strong>Hintergrundfarbe:</strong> <span style="background-color:${ev.color}; padding: 2px 5px;">${ev.color}</span><br>
                        <strong>Schriftfarbe:</strong> <span style="background-color:${ev.txtcolor}; padding: 2px 5px;">${ev.txtcolor}</span><br>
                        <button onclick="deleteEvent(${dayIdx},${evIdx})">Löschen</button>
                    </div>`;
                });
                dayDiv.innerHTML = html;
                eventList.appendChild(dayDiv);
            });
        }

        // Neues Event aus den Eingabefeldern hinzufügen
        function addEvent() {
            const date = document.getElementById('event-date').value;
            const time = document.getElementById('event-time').value;
            const image = document.getElementById('event-image').value;
            const texts = document.getElementById('event-texts').value.split(',').map(t => t.trim()).filter(Boolean);
            const link = document.getElementById('event-link').value;
            const color = document.getElementById('event-color').value;
            const txtcolor = document.getElementById('event-txtcolor').value;

            if (date && texts.length > 0 && link) {
                let day = eventsByDate.find(e => e.date === date);
                if (!day) {
                    day = { date, events: [] };
                    eventsByDate.push(day);
                }
                // Prüfen, ob Event mit gleicher Zeit existiert
                const idx = day.events.findIndex(ev => ev.time === time);
                const newEvent = { time, image, texts, link, color, txtcolor };
                if (idx !== -1) {
                    day.events[idx] = newEvent;
                } else {
                    day.events.push(newEvent);
                }
                // Nach Zeit sortieren
                day.events.sort((a, b) => (a.time || '').localeCompare(b.time || ''));
                // Nach Datum sortieren
                eventsByDate.sort((a, b) => a.date.localeCompare(b.date));
                displayEvents();
            } else {
                alert('Bitte alle Felder ausfüllen.');
            }
        }

        // Event aus der Liste löschen
        function deleteEvent(dayIdx, evIdx) {
            eventsByDate[dayIdx].events.splice(evIdx, 1);
            if (eventsByDate[dayIdx].events.length === 0) {
                eventsByDate.splice(dayIdx, 1);
            }
            displayEvents();
        }

        // Hilfsfunktion: JSON formatieren
        function formatJSON(json) {
            return JSON.stringify(json, null, 2);
        }

        // Events als Datei herunterladen (Dateiname wählbar)
        function downloadEvents() {
            const filename = document.getElementById('event-filename').value || 'events.json';
            const formattedEvents = formatJSON(eventsByDate);
            const blob = new Blob([formattedEvents], { type: 'application/json' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'calendar/' + filename;
            link.click();
        }

        // Events an den Server senden und speichern (Dateiname wählbar)
        async function saveEvents() {
            const filename = document.getElementById('event-filename').value || 'events.json';
            const formattedEvents = formatJSON(eventsByDate);
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
