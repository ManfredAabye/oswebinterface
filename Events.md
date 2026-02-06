# Events.md – Anleitung für das Eventsystem

## Übersicht

Dieses Dokument beschreibt die Funktionsweise des Event-Kalendersystems für das Webinterface. Es werden sowohl die neue (verschachtelte) als auch die alte (flache) Struktur erklärt. Außerdem wird beschrieben, wie man die events.json von Hand bearbeitet und worauf bei der Kompatibilität zu achten ist.

---

## 1. Neue Funktionen (ab 2026)

### Verschachteltes JSON-Format

- **Ein Tag = ein Objekt** mit `date` und einem Array `events`.
- **Mehrere Einträge pro Tag** möglich (z.B. verschiedene Uhrzeiten, Texte, Bilder).
- **Beispiel:**

```json
[
  {
    "date": "2026-02-06",
    "events": [
      {
        "time": "20:00",
        "image": "calendar/Beispielbild1.jpg",
        "texts": ["Titel 1", "Beschreibung 1"],
        "link": "https://beispiel.de/1",
        "color": "#FFCC99",
        "txtcolor": "#000000"
      },
      {
        "time": "21:00",
        "image": "calendar/Beispielbild2.jpg",
        "texts": ["Titel 2", "Beschreibung 2"],
        "link": "https://beispiel.de/2",
        "color": "#99CCFF",
        "txtcolor": "#FFFFFF"
      }
    ]
  },
  ...
]
```

### Vorteile

- Mehrere Events pro Tag, mit Uhrzeit, Bild, Link und Farben.
- Übersichtliche Bearbeitung im Editor (eventedit.php).
- RSS-Feed und Kalender unterstützen alle neuen Felder.

### Hinweise

- **Nicht kompatibel mit der alten flachen Struktur!**
- Die neue Struktur wird von eventedit.php, eventcalendar.php, rss-feed.php und welcomesplashpage.php benötigt.

---

## 2. Alte Funktionen (bis 2025)

### Flaches JSON-Format

- **Jedes Event ist ein eigenes Objekt** mit `date`.
- **Nur ein Event pro Tag möglich** (bzw. mehrere Einträge mit gleichem Datum, aber keine Gruppierung).
- **Beispiel:**

```json
[
  {
    "date": "2025-12-24",
    "texts": ["Heiligabend", "24.12.2025"],
    "image": "calendar/Heiligabend.jpg",
    "link": "https://beispiel.de/alt",
    "color": "#FFCC99",
    "txtcolor": "#000000"
  },
  ...
]
```

### Hinweise2

- Wird von älteren Versionen von eventcalendar.php und rss-feed.php unterstützt.
- **Nicht kompatibel mit der neuen verschachtelten Struktur!**

---

## 3. events.json von Hand bearbeiten

### Grundregeln

- Die Datei ist ein Array (`[]`) von Tagesobjekten.
- Jeder Tag hat das Format:
  - `date`: Datum im Format `YYYY-MM-DD`
  - `events`: Array von Event-Objekten
- Jedes Event-Objekt kann folgende Felder enthalten:
  - `time`: Uhrzeit (optional, Format `HH:MM`)
  - `texts`: Array von Strings (erste Zeile = Titel)
  - `image`: Bildpfad oder URL (optional)
  - `link`: URL (optional)
  - `color`: Hintergrundfarbe (optional, z.B. `#FFCC99`)
  - `txtcolor`: Textfarbe (optional, z.B. `#000000`)

### Beispiel für einen Tag mit mehreren Events

```json
{
  "date": "2026-02-06",
  "events": [
    { "time": "20:00", "texts": ["Event 1"], ... },
    { "time": "21:00", "texts": ["Event 2"], ... }
  ]
}
```

### Tipps

- Achte auf gültiges JSON (keine doppelten Kommas, alle Strings in Anführungszeichen).
- Die Datei kann mit jedem Texteditor bearbeitet werden.
- Nach manuellen Änderungen empfiehlt sich ein Test im Editor (eventedit.php) oder im Kalender.

---

## 4. Kompatibilität

- Die neue verschachtelte Struktur ist **nicht abwärtskompatibel** zur alten flachen Struktur.
- Für die Nutzung der neuen Features müssen alle relevanten Dateien (eventedit.php, eventcalendar.php, rss-feed.php, welcomesplashpage.php) auf die neue Version aktualisiert werden.
- Ein Mischen beider Formate in einer Datei ist nicht möglich.

---

## 5. Fehlerquellen & Hinweise

- Bei Problemen mit der Anzeige: Prüfe die Struktur der events.json und die Browser-Konsole auf Fehler.
- Bei mehreren Einträgen pro Tag: Alle Events müssen im `events`-Array eines Tagesobjekts stehen.
- Bilder müssen im richtigen Pfad liegen oder als vollständige URL angegeben werden.

---
