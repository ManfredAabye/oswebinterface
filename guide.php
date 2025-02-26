<?php
$title = "Guide";
include_once "include/config.php";

// Fehlerbehandlung für die JSON-Datei
$json = file_get_contents('include/destinations.json');
if ($json === false) {
    die('Fehler beim Laden der JSON-Datei.');
}
$data = json_decode($json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die('Fehler beim Dekodieren der JSON-Datei.');
}

// Datenbankverbindung mit Fehlerbehandlung
$con = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die('Fehler bei der Verbindung zur Datenbank: ' . mysqli_connect_error());
}
?>

<html>
<title>Destination Guide</title>
<style>
    body { font-family: Arial, sans-serif; background-color: <?= SECONDARY_COLOR ?>; padding: 10px; color: <?= PRIMARY_COLOR ?>; }
    
    h1 { font-size: 16px; text-align: center; color: <?= PRIMARY_COLOR ?>; }

    .button-container { text-align: center; margin-bottom: 15px; }
    button { padding: 10px; margin: 5px; cursor: pointer; background-color: <?= PRIMARY_COLOR ?>; color: white; border: none; border-radius: 5px; }

    .list-container { display: none; justify-content: flex-start; flex-wrap: nowrap; overflow-x: auto; gap: 10px; border-radius: 5px;}
    .grid-container { display: none; flex-wrap: wrap; gap: 10px; justify-content: center; border-radius: 5px;}

    .region-box { background: <?= HEADER_COLOR ?>; padding: 10px; border-radius: 5px; display: flex; align-items: center; color: <?= PRIMARY_COLOR ?>; }
    .region-icon { margin-right: 5px; font-size: 16px; color: <?= PRIMARY_COLOR ?>; }

    .grid-item { width: 23%; padding: 10px; border: 1px solid #aaa; background: <?= HEADER_COLOR ?>; text-align: center; border-radius: 5px; color: <?= PRIMARY_COLOR ?>; }
    /* .grid-link { display: block; margin-top: 5px; padding: 5px; background: <?= FOOTER_COLOR ?>; color: white; border-radius: 3px; text-decoration: none; } */
    .grid-link { display: block; margin-top: 5px; padding: 5px; background: #0066cc; color: white; border-radius: 3px; text-decoration: none; }

    .search-bar { width: 23%; padding: 10px; border: 1px solid #aaa; background: #0066cc; color: white; text-align: center; border-radius: 5px; }

    .hop-buttons { display: flex; gap: 5px; margin-top: 5px; }
    /* .hop-button { padding: 5px; background: <?= FOOTER_COLOR ?>; color: white; border-radius: 3px; text-decoration: none; } */
    .hop-button { padding: 5px; background: #004080; color: white; border-radius: 5px; text-decoration: none; }
</style>

<body>

    <h1><?php echo SITE_NAME; ?> Destination Guide</h1>

    <!-- Auswahl Buttons -->
    <div class="button-container">
        <button onclick="showJSON()">Regionsliste JSON</button>
        <button onclick="showDatabase()">Regionsliste Database</button>
        <button onclick="showGridList()">Gridliste CSV</button>
    </div>

    <!-- ##########################  JSON-Regionsliste ######################################## -->
    <div id="jsonList" class="guidebody" style="display: <?= (GRIDLIST_VIEW == 'json') ? 'flex' : 'none' ?>; flex-wrap: wrap; gap: 10px; justify-content: flex-start; border-radius: 5px; padding: 10px;">
        <?php foreach ($data as $category => $destinations): ?>
            <fieldset style='flex: 1; min-width: 250px; max-width: 350px;'>
                <legend><?= htmlspecialchars(ucfirst($category)) ?></legend>
                <div class='region-container' style='display: flex; flex-wrap: wrap; gap: 10px; border-radius: 5px; padding: 10px;'>
                    <?php foreach ($destinations as $destination): ?>
                        <div class="region-box">
                            <a href="<?= htmlspecialchars($destination['url']) ?>" target="_blank">
                                <img src="<?= htmlspecialchars($destination['image']) ?>" alt="<?= htmlspecialchars($destination['name']) ?>" width="50" height="50" style="border-radius:5px; margin-right:10px; padding: 10px;">
                            </a>
                            <span><?= htmlspecialchars($destination['name']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>
    </div>


    <!-- ########################################  Datenbank-Regionsliste ######################################## -->
     <!-- todo: Abstand hinzufügen. -->
    <div id="databaseList" class="list-container" style="padding: 10px; display: <?= (GRIDLIST_VIEW == 'database') ? 'flex' : 'none' ?>;">
        <?php
        $sql = "SELECT regionName, serverIP, serverPort FROM regions ORDER BY last_seen DESC LIMIT 10";
        $resultregions = mysqli_query($con, $sql);

        while ($dsatz = mysqli_fetch_assoc($resultregions)) {
            $region = htmlspecialchars($dsatz["regionName"]);
            $ip = htmlspecialchars($dsatz["serverIP"]);
            $port = htmlspecialchars($dsatz["serverPort"]);

            // Link für den "Hop"-Button
            $regionslink = "hop://$ip:$port/$region/128/128/23";

            echo '<div class="region-box" style="display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 10px; border-radius: 5px; padding: 10px;">';
            // Button mit Regionsname
            echo "<button style='margin-bottom: 5px;'>$region</button>";
            // Button mit "Hop"
            echo "<a href='$regionslink' class='hop-button' target='_blank' style='text-align: center;'>Hop $region</a>";
            echo '</div>';
        }

        mysqli_close($con);
        ?>
    </div>





    <!-- ##################### Grid Teleport Liste ######################################## -->
    <div id="gridList" class="grid-container" style="display: <?= (GRIDLIST_VIEW == 'grid') ? 'flex' : 'none' ?>;">
        <div class="search-bar">
        <p>Search</p>
            <input type="text" id="searchInput" onkeyup="filterGrids()" placeholder="Suche nach Grids...">            
        </div>
        <?php
        if (($handle = fopen(GRIDLIST_FILE, "r")) !== false) {
            fgetcsv($handle); // Überspringe die Header-Zeile
            while (($data = fgetcsv($handle)) !== false) {
                $gridName = htmlspecialchars($data[0]);
                $loginURI = htmlspecialchars($data[1]);

                // Erster Link: Direkter Link zum Grid
                $gridlink1 = "secondlife:///app/gridmanager/addgrid/$loginURI";

                // Zweiter Link: Link ohne spezifische Aktion
                // todo: Fehler bei hop: http:// kann auch https:// sein
                $gridlink2 = "hop://http://$loginURI/ ";


                echo '<div class="grid-item">';
                echo "<span>$gridName</span>";
                echo '<div class="hop-buttons">';
                echo "<a href='$gridlink1' class='grid-link' target='_blank' style='width: 60%; text-align: center;'>Viewer Grid Eintrag</a>";
                echo "<a href='$gridlink2' class='grid-link' target='_blank' style='width: 100%; text-align: center;'>Hop $gridName</a>";
                echo '</div>';
                echo '</div>';
            }
            fclose($handle);
        }
        ?>
    </div>

    <script>
        function filterGrids() {
            const input = document.getElementById('searchInput').value.toUpperCase();
            document.querySelectorAll('.grid-item').forEach(item => {
                item.style.display = item.textContent.toUpperCase().includes(input) ? "" : "none";
            });
        }

        function showJSON() {
            document.getElementById('jsonList').style.display = 'flex';
            document.getElementById('databaseList').style.display = 'none';
            document.getElementById('gridList').style.display = 'none';
        }

        function showDatabase() {
            document.getElementById('jsonList').style.display = 'none';
            document.getElementById('databaseList').style.display = 'flex';
            document.getElementById('gridList').style.display = 'none';
        }

        function showGridList() {
            document.getElementById('jsonList').style.display = 'none';
            document.getElementById('databaseList').style.display = 'none';
            document.getElementById('gridList').style.display = 'flex';
        }

        document.addEventListener('DOMContentLoaded', () => {
            <?php if (GRIDLIST_VIEW == 'json'): ?>
                showJSON();
            <?php elseif (GRIDLIST_VIEW == 'database'): ?>
                showDatabase();
            <?php elseif (GRIDLIST_VIEW == 'grid'): ?>
                showGridList();
            <?php endif; ?>
        });
    </script>

</body>
</html>