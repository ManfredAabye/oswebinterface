<?php
$title = "Help";
include_once 'include/header.php';
$txt1 = BASE_URL;
$txt2 = GRID_PORT;
?>
<style>
   .container {
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

<main class="container">
<h1>OpenSim Viewer mit einem Grid verbinden</h1>

<h2>Schritt-für-Schritt Anleitung (Deutsch)</h2>
<ol>
    <li>Lade einen kompatiblen OpenSim-Viewer herunter (z. B. Firestorm).</li>
    <li>Installiere den Viewer auf deinem Computer.</li>
    <li>Starte den Viewer und öffne die Einstellungen.</li>
    <li>Suche den Bereich <strong>"Grids"</strong> oder <strong>"Grid-Manager"</strong>.</li>
    <li>Klicke auf <strong>"Neues Grid hinzufügen"</strong> oder eine ähnliche Option.</li>
    <li>Gib die <strong>Login-URL</strong> deines Grids ein (z. B. <code><?php echo $txt1, $txt2; ?>/</code>).</li>
    <li>Klicke auf <strong>"Hinzufügen" oder "Speichern"</strong>.</li>
    <li>Wähle das Grid aus der Liste und gib deine Anmeldedaten ein.</li>
    <li>Klicke auf <strong>"Anmelden"</strong>, um das Grid zu betreten.</li>
</ol>

<h2>Tipps</h2>
<ul>
    <li>Stelle sicher, dass du die richtige Grid-URL hast.</li>
    <li>Falls der Viewer das Grid nicht erkennt, prüfe die Serververbindung.</li>
    <li>Nutze die neueste Version deines Viewers für beste Kompatibilität.</li>
</ul>

<hr>

<h1>Connecting an OpenSim Viewer to a Grid</h1>

<h2>Step-by-Step Guide (English)</h2>
<ol>
    <li>Download a compatible OpenSim viewer (e.g., Firestorm).</li>
    <li>Install the viewer on your computer.</li>
    <li>Start the viewer and open the settings.</li>
    <li>Look for the <strong>"Grids"</strong> or <strong>"Grid Manager"</strong> section.</li>
    <li>Click on <strong>"Add New Grid"</strong> or a similar option.</li>
    <li>Enter the <strong>login URL</strong> of your grid (e.g., <code><?php echo $txt1, $txt2; ?>/</code>).</li>
    <li>Click <strong>"Add" or "Save"</strong>.</li>
    <li>Select the grid from the list and enter your login credentials.</li>
    <li>Click <strong>"Login"</strong> to enter the grid.</li>
</ol>

<h2>Tips</h2>
<ul>
    <li>Make sure you have the correct grid URL.</li>
    <li>If the viewer does not recognize the grid, check the server connection.</li>
    <li>Use the latest version of your viewer for best compatibility.</li>
</ul>
</main>
<br><br><br>
