<?php
$title = "TOS";
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
### **Terms of Service (Nutzungsbedingungen) für [DEIN GRID-NAME]**  

**Letzte Aktualisierung:** [DATUM]  

Willkommen bei **[DEIN GRID-NAME]**! Diese Nutzungsbedingungen regeln deine Nutzung unserer virtuellen Welt, 
die auf OpenSimulator basiert. Durch die Registrierung und Nutzung unseres Dienstes stimmst du diesen Bedingungen zu. 
Falls du nicht einverstanden bist, darfst du unser Grid nicht nutzen.  

---

## **1. Allgemeine Bestimmungen**  
1.1 **Geltungsbereich:** Diese Bedingungen gelten für alle Nutzer unseres OpenSimulator-Grids, einschließlich Gäste und registrierte Benutzer.  
1.2 **Änderungen:** Wir behalten uns das Recht vor, diese Bedingungen jederzeit zu ändern. Änderungen treten mit ihrer Veröffentlichung auf unserer Webseite in Kraft.  
1.3 **Zustimmung:** Mit der Nutzung unseres Grids erklärst du dich mit den aktuellen Nutzungsbedingungen einverstanden.  

---

## **2. Benutzerkonten und Zugang**  
2.1 **Registrierung:**  
- Du musst mindestens **[ALTER, z. B. 16]** Jahre alt sein, um ein Konto zu erstellen.  
- Die Angabe korrekter und vollständiger Informationen ist erforderlich.  
- Pro Person ist nur ein Benutzerkonto erlaubt, es sei denn, wir gestatten ausdrücklich mehrere Konten.  

2.2 **Sicherheit deines Kontos:**  
- Du bist für die Sicherheit deines Kontos und Passworts verantwortlich.  
- Falls du einen unbefugten Zugriff auf dein Konto vermutest, informiere uns umgehend.  

2.3 **Konto-Sperrung & Löschung:**  
- Wir behalten uns das Recht vor, dein Konto zu sperren oder zu löschen, wenn du gegen diese TOS verstößt.  
- Inaktive Konten können nach **[z. B. 6 Monaten]** ohne Vorwarnung gelöscht werden.  

---

## **3. Virtuelle Inhalte und Wirtschaftssystem**  
3.1 **Eigentum an Inhalten:**  
- Inhalte, die du erstellst oder hochlädst, bleiben dein geistiges Eigentum.  
- Du gewährst uns jedoch das Recht, deine Inhalte innerhalb des Grids zu nutzen, zu hosten und zu verwalten.  

3.2 **Urheberrechte und Lizenzierung:**  
- Es ist verboten, urheberrechtlich geschützte Inhalte ohne Erlaubnis hochzuladen.  
- Falls ein Nutzer Urheberrechte verletzt, behalten wir uns vor, den Inhalt zu entfernen und das Konto zu sperren.  

3.3 **Virtuelle Währung & Transaktionen:**  
- Falls unser Grid eine virtuelle Währung verwendet (**z. B. Gloebits, Podex, OMC**), erkennst du an, dass diese keinen echten Geldwert hat.  
- Wir übernehmen keine Verantwortung für Verluste oder technische Fehler im Zusammenhang mit virtuellen Währungen oder Transaktionen.  

---

## **4. Verhaltensregeln**  
4.1 **Erlaubte und verbotene Inhalte:**  
- Keine **Belästigung, Hassrede, rassistischen oder sexuell expliziten Inhalte**, die gegen geltendes Recht verstoßen.  
- Kein **Spam, Betrug oder das Nachahmen anderer Nutzer/Admins**.  
- Kein **Hacking, Griefing oder das Stören anderer Nutzer** durch Skripte, Exploits oder Angriffe.  

4.2 **Regionale und Parzellenregeln:**  
- Eigentümer von Regionen/Parzellen dürfen eigene Regeln festlegen, solange sie nicht gegen diese TOS verstoßen.  
- Admins behalten sich das Recht vor, störende Inhalte oder Regionen zu entfernen.  

---

## **5. Datenschutz und Datenspeicherung**  
5.1 **Gespeicherte Daten:**  
- Wir speichern deine Account-Informationen, IP-Adresse, Avatar-Daten und In-World-Interaktionen für administrative Zwecke.  
- Persönliche Daten werden nicht an Dritte weitergegeben, es sei denn, es besteht eine gesetzliche Verpflichtung.  

5.2 **Nutzung von Drittanbietern:**  
- Falls unser Grid externe Dienste wie **Hypergrid-Teleports, Gloebits oder PayPal** verwendet, gelten deren Datenschutzrichtlinien zusätzlich.  
- Beim Verlassen unseres Grids über Hypergrid-Teleports können deine Daten von anderen Grids verarbeitet werden.  

---

## **6. DMCA-Richtlinie (Urheberrechtsschutz)**  
Falls du glaubst, dass Inhalte in unserem Grid gegen dein Urheberrecht verstoßen, kannst du eine **DMCA-Beschwerde** einreichen:  

### **6.1 Wie du eine DMCA-Meldung einreichst:**  
Sende eine schriftliche Beschwerde an **[DEINE KONTAKT-EMAIL]** mit folgenden Angaben:  
1. Eine Beschreibung des geschützten Werkes, das verletzt wurde.  
2. Die genaue Position des beanstandeten Inhalts (Region, Koordinaten, UUID, Screenshots).  
3. Deine Kontaktdaten (Name, E-Mail, Adresse, Telefonnummer).  
4. Eine Erklärung, dass du in gutem Glauben handelst und der Inhalt unrechtmäßig verwendet wird.  
5. Eine eidesstattliche Versicherung, dass deine Angaben korrekt sind.  

### **6.2 Reaktion auf DMCA-Meldungen:**  
- Wir werden überprüfte Verstöße entfernen.  
- Falls du fälschlicherweise als Urheberrechtsverletzer gemeldet wurdest, kannst du eine Gegendarstellung einreichen.  

---

## **7. Haftungsausschluss und Beendigung des Dienstes**  
7.1 **Haftungsausschluss:**  
- Unser Grid wird wie besehen zur Verfügung gestellt. Wir garantieren keine **störungsfreie, fehlerfreie oder ununterbrochene Nutzung**.  
- Wir haften nicht für **Datenverluste, Hacks oder technische Probleme**.  

7.2 **Beendigung des Dienstes:**  
- Wir behalten uns das Recht vor, das Grid oder Teile davon jederzeit einzustellen.  
- Bei einer Schließung gibt es keinen Anspruch auf Erstattung virtueller Guthaben oder Inhalte.  

---

## **8. Kontakt**  
Falls du Fragen zu diesen Nutzungsbedingungen hast, kannst du uns unter **[DEINE KONTAKT-EMAIL]** erreichen.  

---

### **Akzeptanz der Nutzungsbedingungen**  
Mit der Nutzung von **[DEIN GRID-NAME]** erklärst du dich mit diesen Nutzungsbedingungen einverstanden. Falls du nicht einverstanden bist, verlasse das Grid und lösche dein Konto.
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
