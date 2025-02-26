<?php
$title = "DMCA";
include 'header.php';
?>

<style>
   .markdown-content {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
      Color:rgb(31, 31, 31);
      background-color:rgb(238, 241, 241);
      border: 1px solid #ddd;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
   }
</style>

<?php
function replacePlaceholders($text, $variables = []) {
    foreach ($variables as $key => $value) {
        $text = str_replace("[$key]", $value, $text);
    }
    return $text;
}

function simpleMarkdownToHTML($text) {
    // Zeilenumbrüche umwandeln
    $text = nl2br($text);

    // Fettdruck **text** -> <strong>text</strong>
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);

    // Kursiv *text* -> <em>text</em>
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);

    // Überschriften # -> <h1>, ## -> <h2> usw.
    for ($i = 6; $i >= 1; $i--) {
        $text = preg_replace('/^' . str_repeat('#', $i) . '\s*(.*?)$/m', "<h$i>$1</h$i>", $text);
    }

    // Links [Text](URL) -> <a href="URL">Text</a>
    $text = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>', $text);

    // Inline-Code `text` -> <code>text</code>
    $text = preg_replace('/`(.*?)`/', '<code>$1</code>', $text);

    return $text;
}

// Deine Markdown-TOS mit Platzhaltern
$tosText = "
# **DMCA-Richtlinie für [DEIN GRID-NAME]**  
**Letzte Aktualisierung:** [DATUM]  

Diese DMCA-Richtlinie beschreibt, wie **[DEIN GRID-NAME]** (nachfolgend „wir“, „uns“, „unser Grid“) 
auf Beschwerden bezüglich Urheberrechtsverletzungen gemäß dem **Digital Millennium Copyright Act (DMCA)** reagiert.  

Falls du glaubst, dass Inhalte innerhalb unseres OpenSimulator-Grids gegen dein Urheberrecht verstoßen, 
kannst du eine **DMCA-Beschwerde** einreichen, indem du die unten beschriebenen Schritte befolgst.  

---

## **1. Einreichen einer DMCA-Beschwerde (Takedown-Anfrage)**  
Falls du der Meinung bist, dass deine urheberrechtlich geschützten Inhalte ohne Erlaubnis in **[DEIN GRID-NAME]** verwendet wurden, 
sende bitte eine schriftliche Beschwerde an unseren **DMCA-Agenten** unter:  

📩 **E-Mail:** [DEINE EMAIL]  
📬 **Postadresse:** [DEINE POSTADRESSE]  
📞 **Telefon:** [OPTIONAL]  

**Deine DMCA-Beschwerde muss folgende Informationen enthalten:**  

1. **Identifikation des geschützten Werkes:**  
   - Eine detaillierte Beschreibung des urheberrechtlich geschützten Werkes (z. B. ein Screenshot, Link oder Dokumentation).  
   
2. **Standort des rechtsverletzenden Inhalts:**  
   - Genaue Position des Inhalts in unserem Grid, inklusive:  
     - Name der Region  
     - Koordinaten (falls möglich)  
     - UUID oder Asset-ID des betroffenen Objekts  
     - Screenshot oder Beschreibung  

3. **Kontaktdaten:**  
   - Dein Name, Adresse, E-Mail-Adresse und Telefonnummer.  

4. **Erklärung zur Rechtsverletzung:**  
   - Eine Erklärung, dass du in **gutem Glauben** davon ausgehst, dass die Nutzung nicht vom Urheberrechtsinhaber, 
   seinem Vertreter oder dem Gesetz (z. B. Fair Use) erlaubt ist.  

5. **Eidesstattliche Versicherung:**  
   - Eine Erklärung, dass die Angaben in deiner Beschwerde korrekt sind und du der rechtmäßige Inhaber des Urheberrechts oder ein autorisierter Vertreter bist.  

6. **Digitale oder physische Unterschrift:**  
   - Eine elektronische oder handschriftliche Unterschrift des Urheberrechtsinhabers oder dessen bevollmächtigten Vertreters.  

---

## **2. Unsere Reaktion auf eine DMCA-Beschwerde**  
Nach Erhalt einer gültigen DMCA-Anfrage werden wir:  

- Den mutmaßlich rechtsverletzenden Inhalt **vorläufig entfernen oder den Zugriff darauf sperren**.  
- Den Nutzer, der den Inhalt bereitgestellt hat, über die Beschwerde informieren.  
- Falls der betroffene Nutzer eine **Gegendarstellung** einreicht (siehe Abschnitt 3), den Urheberrechtsinhaber darüber informieren.  

Falls der Urheberrechtsinhaber innerhalb von **10 Werktagen** nach unserer Benachrichtigung keine rechtlichen Schritte einleitet, 
können wir den entfernten Inhalt wiederherstellen.  

---

## **3. Einreichen einer Gegendarstellung (Counter-Notice)**  
Falls du der Meinung bist, dass der entfernte Inhalt nicht gegen Urheberrechte verstößt oder du eine Erlaubnis zur Nutzung hattest, 
kannst du eine **Gegendarstellung** einreichen.  

Bitte sende deine Gegendarstellung an **[DEINE EMAIL]** mit folgenden Angaben:  

1. **Identifikation des entfernten Inhalts:**  
   - Die ursprüngliche Position des Inhalts (Region, Koordinaten, UUID, Screenshots).  

2. **Erklärung zur Unrechtmäßigkeit der Beschwerde:**  
   - Eine Erklärung, dass du **in gutem Glauben** der Meinung bist, dass die Entfernung aufgrund eines Fehlers oder falscher Identifizierung erfolgte.  

3. **Zustimmung zur Gerichtsbarkeit:**  
   - Falls du außerhalb der USA lebst, eine Erklärung, dass du die Gerichtsbarkeit der US-amerikanischen Bundesgerichte akzeptierst.  

4. **Eidesstattliche Versicherung:**  
   - Eine Versicherung, dass deine Angaben korrekt sind und du für eventuelle rechtliche Konsequenzen verantwortlich bist.  

5. **Digitale oder physische Unterschrift:**  
   - Deine Unterschrift oder die deines bevollmächtigten Vertreters.  

Nach Erhalt einer gültigen Gegendarstellung kann der entfernte Inhalt innerhalb von **10–14 Tagen** wiederhergestellt werden, 
sofern der ursprüngliche Beschwerdeführer keine Klage einreicht.  

---

## **4. Konsequenzen für wiederholte Verstöße**  
Nutzer, die wiederholt Urheberrechte verletzen, können:  
- **Verwarnt**,  
- **Vorübergehend gesperrt**,  
- Oder **dauerhaft aus dem Grid ausgeschlossen** werden.  

Wir behalten uns das Recht vor, Nutzerkonten ohne vorherige Warnung zu sperren, falls schwere Verstöße vorliegen.  

---

## **5. Keine Haftung für Nutzerinhalte**  
Unser Grid stellt eine Plattform für virtuelle Interaktionen bereit und hostet nutzergenerierte Inhalte. 
Wir übernehmen keine Haftung für urheberrechtlich geschützte Materialien, die von Nutzern hochgeladen wurden. 
Alle Nutzer sind für ihre hochgeladenen Inhalte selbst verantwortlich.  

Wir arbeiten jedoch aktiv daran, **rechtsverletzende Inhalte zu entfernen**, sobald wir eine **gültige DMCA-Beschwerde** erhalten.  

---

## **6. Kontakt für weitere Fragen**  
Falls du Fragen zur DMCA-Richtlinie hast, kannst du uns unter **[DEINE KONTAKT-EMAIL]** erreichen.  

---

### **Rechtlicher Hinweis:**  
Diese Vorlage dient nur zu **Informationszwecken** und stellt **keine Rechtsberatung** dar. 
Falls du eine rechtlich geprüfte DMCA-Richtlinie benötigst, konsultiere bitte einen Anwalt.  
";

// Platzhalter-Werte setzen
$variables = [
    "DEIN GRID-NAME" => "Virtual " . SITE_NAME ." Grid",
    "DATUM" => date("d.m.Y"),
    "DEINE EMAIL" => "support@" . SITE_NAME .".com",
    "DEINE POSTADRESSE" => "Musterstraße 123, 12345 Musterstadt",
    "OPTIONAL" => "Discord: GridSupport" . BASE_URL ."",
    "DEINE KONTAKT-EMAIL" => "kontakt@" . SITE_NAME .".com"
];

// 1️⃣ Platzhalter ersetzen
$tosText = replacePlaceholders($tosText, $variables);

// 2️⃣ Markdown in HTML umwandeln
$finalHTML = simpleMarkdownToHTML($tosText);

// 3️⃣ HTML ausgeben
echo "<div class='markdown-content'>$finalHTML</div>";
?>
<br><br><br><br>
