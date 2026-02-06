<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="dynamic-title">Events Calendar</title>
    <style>
        /* Grundlegende Seiteneinstellungen */
        body { 
            font-family: Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            padding: 20px; 
        }
        
        /* Kalender-Container */
        .calendar { 
            width: 100%; 
            max-width: 1150px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
        }
        
        /* Kopfzeile mit Monatsnavigation */
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            background: #007bff; 
            color: #fff; 
            padding: 10px; 
        }
        
        /* Wochentage-Zeile */
        .days { 
            display: grid; 
            grid-template-columns: repeat(7, 1fr); 
            background: #f0f0f0; 
        }
        
        /* Einzelner Wochentag */
        .day { 
            text-align: center; 
            padding: 8px 0; 
            font-weight: bold; 
        }
        
        /* Container für alle Datumszellen */
        .dates { 
            display: grid; 
            grid-template-columns: repeat(7, 1fr); 
        }
        
        /* Einzelne Datumszelle */
        .date { 
            text-align: left; 
            padding: 10px; 
            border: 1px solid #ccc; 
            position: relative; 
            aspect-ratio: 1 / 1; /* Macht die Zelle quadratisch */
            background-size: cover; 
            background-position: center; 
            display: flex; 
            flex-direction: column; 
            font-size: 0.7em; 
        }
        
        /* Tag-Nummer in der Zelle */
        .date-number { 
            align-self: flex-start; 
            font-size: 1.4em; 
            z-index: 1; /* Über Bildern */
        }
        
        /* Event-Container am Ende der Zelle */
        .event { 
            font-size: 0.8em; 
            background-color: rgba(0, 0, 0, 0.7); /* Weniger Transparenz, schwarzer Hintergrund */
            padding: 2px 4px; 
            border-radius: 3px; 
            margin-top: auto; /* Nach unten schieben */
            text-align: center; 
            z-index: 1; 
        }
        
        /* Event-Box mit Bild als Hintergrund */
        .event.has-bgimg .event-content {
            background: rgba(0, 0, 0, 0.7); /* Weniger Transparenz, schwarzer Hintergrund */
        }
        
        /* Event-Textzeile */
        .event-text { 
            margin: 2px 0; 
            z-index: 1; 
        }
        
        /* Fett gedruckter Event-Text (erste Zeile) */
        .event-text-bold { 
            font-weight: bold; 
        }
        
        /* Container für Hintergrundbilder */
        .image-container { 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            overflow: hidden; 
        }
        
        /* Styling für Hintergrundbilder */
        .image-container img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; /* Bild proportional zuschneiden */
        }
        
        /* Link-Stil innerhalb von Events */
        .event-link { 
            font-weight: bold; 
            text-align: center; 
            display: block; 
            margin-top: auto; 
        }

        /* Event-Bild als Hintergrundbild der Zelle */
        .event.has-bgimg {
            background-size: cover !important;
            background-position: center !important;
            min-height: 120px;
            min-width: 120px;
            position: relative;
        }
        /* .event.has-bgimg .event-content {
            position: relative;
            z-index: 2;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 6px;
            padding: 4px;
            margin: 0;
        } */
        .event.has-bgimg a.event-img-link, .event.has-bgimg img.event-img {
            display: none !important;
        }

        /* Abstand zwischen Tageseinträgen (Events) */
        .events-container .event {
            margin-bottom: 4px;
        }
        .events-container .event:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <!-- Hauptkalender-Container -->
    <div class="calendar">
        <!-- Kopfzeile mit Navigation -->
        <div class="header">
            <!-- Button für vorherigen Monat -->
            <button onclick="changeMonth(-1)">&lt;</button>
            <!-- Anzeige von Monat und Jahr -->
            <h2 id="month-year"></h2>
            <!-- Button für nächsten Monat -->
            <button onclick="changeMonth(1)">&gt;</button>
        </div>
        
        <!-- Zeile mit Wochentagen -->
        <div class="days">
            <div class="day">Mo</div>
            <div class="day">Di</div>
            <div class="day">Mi</div>
            <div class="day">Do</div>
            <div class="day">Fr</div>
            <div class="day">Sa</div>
            <div class="day">So</div>
        </div>
        
        <!-- Container für alle Datumszellen (wird dynamisch gefüllt) -->
        <div id="dates" class="dates"></div>
    </div>

    <script>
        // DOM-Elemente referenzieren
        const monthYear = document.getElementById('month-year');
        const dates = document.getElementById('dates');
        
        // Aktuelles Datum für die Navigation
        let currentDate = new Date();

        /**
         * Lädt Event-Daten für das aktuelle Jahr
         * @returns {Promise<Array>} Array mit Event-Daten
         */
        async function fetchEvents(year) {
            const cacheBuster = `?_=${Date.now()}`;
            try {
                //const response = await fetch(`calendar/events${year}.json${cacheBuster}`);
                const response = await fetch('calendar/events.json' + cacheBuster);
                if (!response.ok) throw new Error('Not found');
                return await response.json();
            } catch (e) {
                const response = await fetch('calendar/events.json' + cacheBuster);
                return await response.json();
            }
        }

        /**
         * Rendert den kompletten Kalender für den aktuellen Monat
         */
        async function renderCalendar() {
            // Extrahiere Jahr und Monat
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            // Berechnungen für Kalenderlayout
            const firstDay = new Date(year, month, 1).getDay(); // Wochentag des 1. des Monats
            const daysInMonth = new Date(year, month + 1, 0).getDate(); // Anzahl Tage im Monat
            // Anpassung für Montag als ersten Wochentag
            const startDay = (firstDay === 0) ? 6 : firstDay - 1;

            // Monatsanzeige aktualisieren (deutsches Format)
            monthYear.textContent = currentDate.toLocaleDateString('de-DE', { 
                month: 'long', 
                year: 'numeric' 
            });
            
            // Seitentitel aktualisieren
            document.getElementById('dynamic-title').textContent = `Events Calendar ${year}`;
            
            // Alte Datumszellen löschen
            dates.innerHTML = '';

            // Event-Daten laden
            const events = await fetchEvents(year);

            // Leere Zellen für Tage vor dem 1. des Monats
            for (let i = 0; i < startDay; i++) {
                const emptyCell = document.createElement('div');
                dates.appendChild(emptyCell);
            }

            // Datumszellen für jeden Tag des Monats erstellen
            for (let day = 1; day <= daysInMonth; day++) {
                const dateCell = document.createElement('div');
                dateCell.classList.add('date');

                // Tag-Nummer hinzufügen
                const dateNumber = document.createElement('div');
                dateNumber.classList.add('date-number');
                dateNumber.textContent = day;
                dateCell.appendChild(dateNumber);

                // Alle Events für diesen Tag suchen (verschachteltes Format)
                const dateStr = new Date(year, month, day).toISOString().slice(0, 10);
                const dayEntry = events.find(e => e.date === dateStr);
                if (dayEntry && Array.isArray(dayEntry.events)) {
                    // Container für alle Events dieses Tages
                    const eventsContainer = document.createElement('div');
                    eventsContainer.classList.add('events-container');
                    dayEntry.events.sort((a, b) => (a.time || '').localeCompare(b.time || ''));
                    dayEntry.events.forEach(event => {
                        // Einzelnes Event-Element
                        const eventBlock = document.createElement('div');
                        eventBlock.classList.add('event');
                        // Bild als Hintergrund, falls vorhanden
                        if (event.image) {
                            eventBlock.classList.add('has-bgimg');
                            eventBlock.style.backgroundImage = `url('${event.image}')`;
                        }
                        // Container für Event-Inhalt (Text, Uhrzeit, Link)
                        const eventContent = document.createElement('div');
                        eventContent.classList.add('event-content');
                        // Uhrzeit
                        if (event.time) {
                            const timeElement = document.createElement('span');
                            timeElement.classList.add('event-text', 'event-time');
                            timeElement.style.fontWeight = 'bold';
                            timeElement.textContent = event.time + ' ';
                            eventContent.appendChild(timeElement);
                        }
                        // Event-Texte (erste Zeile fett)
                        if (event.texts) {
                            event.texts.forEach((text, index) => {
                                const eventTextElement = document.createElement('span');
                                eventTextElement.classList.add('event-text');
                                if (index === 0) eventTextElement.classList.add('event-text-bold');
                                eventTextElement.textContent = text + ' ';
                                eventContent.appendChild(eventTextElement);
                            });
                        }
                        // Link
                        if (event.link) {
                            const lastWord = event.link.split('/').pop();
                            const linkElement = document.createElement('a');
                            linkElement.href = event.link;
                            linkElement.target = '_blank';
                            linkElement.classList.add('event-link');
                            linkElement.textContent = lastWord;
                            eventContent.appendChild(linkElement);
                        }
                        // Bild als Link (nur für Klick, nicht sichtbar, aber für Originalbild im neuen Tab)
                        if (event.image) {
                            const link = document.createElement('a');
                            link.href = event.image;
                            link.target = '_blank';
                            link.rel = 'noopener noreferrer';
                            link.classList.add('event-img-link');
                            // Unsichtbares Bild für Accessibility (optional)
                            const img = document.createElement('img');
                            img.src = event.image;
                            img.classList.add('event-img');
                            img.alt = 'Event Bild';
                            img.style.width = '1px';
                            img.style.height = '1px';
                            img.style.opacity = '0';
                            link.appendChild(img);
                            eventContent.appendChild(link);
                        }
                        // Farben (optional)
                        if (event.color) eventBlock.style.backgroundColor = event.color;
                        if (event.txtcolor) eventBlock.style.color = event.txtcolor;
                        eventBlock.appendChild(eventContent);
                        eventsContainer.appendChild(eventBlock);
                    });
                    dateCell.appendChild(eventsContainer);
                }

                // Fertige Zelle zum Kalender hinzufügen
                dates.appendChild(dateCell);
            }
        }

        /**
         * Wechselt zum vorherigen/nächsten Monat
         * @param {number} offset - -1 für vorherigen, +1 für nächsten Monat
         */
        function changeMonth(offset) {
            currentDate.setMonth(currentDate.getMonth() + offset);
            renderCalendar();
        }

        // Kalender initial rendern
        renderCalendar();
    </script>
</body>
</html>