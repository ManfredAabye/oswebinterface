<?php
$title = "Welcome";
include_once "include/config.php";
// Version: 1.7.0
?>

<html>

<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo LOGO_FONT; ?>&display=swap" rel="stylesheet">

    <style>
        .logofont { font-family: '<?php echo LOGO_FONT; ?>', sans-serif; }
        #welcome-text { font-family: '<?php echo LOGO_FONT; ?>', sans-serif; }
        .bodysplash, html { margin: 0; padding: 0; overflow: hidden; width: 100%; height: 100%; font-family: <?php echo FONT_FAMILY; ?>; background: <?php echo SECONDARY_COLOR; ?>; }
        #background1 { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; object-fit: cover; }
        .info-box { position: absolute; right: 10px; width: 220px; background: rgba(44, 42, 42, 0.4); padding: 10px; border-radius: 8px; border: 1px solid <?php echo LINK_COLOR; ?>; font-size: 14px; color: white; }
        #stats1 { top: 20px; }
        #regionslist { top: 250px; }
        fieldset { border: 1px solid <?php echo LINK_COLOR; ?>; border-radius: 6px; padding: 6px; }
        legend { font-size: 14px; font-weight: bold; color: <?php echo PRIMARY_COLOR_LOGO; ?>; }
        .region-link { font-size: 13px; color: rgb(255, 255, 255); text-decoration: none; display: block; padding: 2px 0; }
        .region-link:hover { text-decoration: underline; color: <?php echo LINK_HOVER_COLOR; ?>; }
        .PictureSlider { position: absolute; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 2s ease-in-out; }
        .PictureSlider.active { opacity: 1; }
        #mainsplash { word-wrap: break-word; width: 60%; position: relative; z-index: 1; top: 20px; left: 20px; text-align: left; color: <?php echo WELCOME_TEXT_COLOR; ?>; font-size: calc(<?php echo WELCOME_TEXT_FONT_SIZE; ?> * 2); font-family: <?php echo FONT_FAMILY; ?>; font-weight: bold; text-shadow: 2px 2px black;}
        .daily-box { position: absolute; left: 15%; top: 33%; width: 420px; background: rgba(44, 42, 42, 0.4); padding: 10px; border-radius: 8px; border: 1px solid <?php echo LINK_COLOR; ?>; font-size: 18px; color: white; }
        .dailyupdatelist { position: relative; z-index: 1; top: 20px; left: 20px; text-align: left; background-color: rgba(128, 128, 128, 0.5); border-radius: 8px; padding: 20px; margin: 20px; max-width: 400px; color: white; font-family: Arial, sans-serif; }
    </style>
</head>

<bodysplash>

    <?php 
    $allebilder = scandir(SLIDESHOW_FOLDER); 
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif']; // Erlaubte Bildformate
    ?>
    <div id="background1">
        <?php
        foreach ($allebilder as $bild) {
            $bildinfo = pathinfo(SLIDESHOW_FOLDER . "/" . $bild);
            // Überprüfe, ob die Datei keine Ordner ist und ob sie ein erlaubtes Bildformat hat
            if (!in_array($bild, [".", "..", "_notes"]) && $bildinfo['basename'] !== "Thumbs.db" && in_array(strtolower($bildinfo['extension']), $allowed_extensions)) {
                ?>
                <img class="PictureSlider" src="<?php echo SLIDESHOW_FOLDER . "/" . $bild; ?>" alt="slide">
                <?php
            }
        }
        ?>
    </div>

    <!-- Logo oder Begrüßungstext -->
    <div id="mainsplash">
        <?php if (LOGO_ON === 'ON') { ?>
            <img src="<?php echo LOGO_PATH; ?>" width="<?php echo LOGO_WIDTH; ?>" height="<?php echo LOGO_HEIGHT; ?>" alt="Logo">
        <?php } ?>

        <?php if (TEXT_ON === 'ON') { ?>
            <div id="welcome-text">
                <?php echo WELCOME_TEXT; ?>
            </div>
        <?php } ?>
    </div>

    <!-- Statistik -->
    <div id='stats1' class="info-box">
        <fieldset>
            <legend>📊 Statistik</legend>
            <?php
            $con = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

            if (!$con) {
                echo "<b style='color: red;'>❌ Grid ist OFFLINE</b>";
            } else {
                $totalUsers = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM Presence"))[0];
                $totalRegions = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM regions"))[0];
                $totalAccounts = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM UserAccounts"))[0];
                $activeUsers = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM GridUser WHERE Login > (UNIX_TIMESTAMP() - (30*86400))"))[0];
                $totalGridAccounts = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM GridUser"))[0];

                echo "<b>Nutzer im Grid:</b> $totalUsers<br>";
                echo "<b>Regionen:</b> $totalRegions<br>";
                echo "<b>Aktiv (30 Tage):</b> $activeUsers<br>";
                echo "<b>Inworld Nutzer:</b> $totalAccounts<br>";
                echo "<b>HG Grid Nutzer:</b> $totalGridAccounts<br>";
                echo "<b style='color: green;'>✔ Grid ist ONLINE</b>";

                mysqli_close($con);
            }
            ?>
        </fieldset>
    </div>

    <!-- Regionsliste -->
    <div id='regionslist' class="info-box">
        <fieldset>
            <legend>🌍 Regionen</legend>
            <?php
            $con = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
            $sql = "SELECT regionName, serverIP, serverPort FROM regions ORDER BY last_seen DESC LIMIT 40";
            $resultregions = mysqli_query($con, $sql);

            while ($dsatz = mysqli_fetch_assoc($resultregions)) {
                $region = htmlspecialchars($dsatz["regionName"]);
                $ip = htmlspecialchars($dsatz["serverIP"]);
                $port = htmlspecialchars($dsatz["serverPort"]);

                // Standard-Koordinaten für den Teleport-Link (X=103, Y=113, Z=23)
                $baseurl_clean = preg_replace('#^https?://#', '', BASE_URL);
                $regionslink = "hop://" . $baseurl_clean . ":" . BASE_PORT . "/$region/103/113/23";

                echo "<a class='region-link' href='$regionslink' target='_blank'>$region</a>";
            }

            mysqli_close($con);
            ?>
        </fieldset>
    </div>

<!-- Tagesaktuelle Einblendungen -->
<?php if (SHOW_DAILY_UPDATE) { ?>
    <div id='dailyupdatelist' class="daily-box">
        <fieldset>
            <legend>🌟 <?php echo SITE_NAME; ?> Aktuell</legend>
            <div class="card" id="daily-update">
                <?php if (DAILY_UPDATE_TYPE === 'text') { ?>
                    <p><?php echo DAILYTEXT; ?></p>
                <?php } else if (DAILY_UPDATE_TYPE === 'rss') { ?>
                    <?php
                    $feedcache_path = FEED_CACHE_PATH;
                    $feedcache_max_age = FEED_CACHE_MAX_AGE;
                    $feed_urls = [RSS_FEED_URL];

                    // Prüfen, ob Cache neu geladen werden muss
                    if (!file_exists($feedcache_path) or filemtime($feedcache_path) < (time() - $feedcache_max_age)) {
                        $output = '';

                        foreach ($feed_urls as $feed_url) {
                            // Feed abrufen
                            $xml = @simplexml_load_string(file_get_contents($feed_url));

                            if (!$xml) {
                                $output .= "<p>Fehler beim Laden des Feeds: <strong>" . htmlspecialchars($feed_url) . "</strong></p>";
                                continue;
                            }

                            $output .= '<h2>' . htmlspecialchars($xml->channel->title) . '</h2>';
                            //$output .= '<p><a href="' . htmlspecialchars($xml->channel->link) . '">Feed öffnen</a></p>';

                            // Alle Einträge des Feeds anzeigen
                            foreach ($xml->channel->item as $entry) {
                                $date = date('d.m.Y', strtotime($entry->pubDate));
                                $title = htmlspecialchars($entry->title);
                                $description = html_entity_decode($entry->description);
                                $time = '';
                                if (preg_match('/\d{2}:\d{2}/', $entry->pubDate, $matches)) {
                                    $time = $matches[0];
                                }
                                $image = '';
                                if (isset($entry->enclosure) && $entry->enclosure['url']) {
                                    $image = htmlspecialchars($entry->enclosure['url']);
                                }
                                if (isset($entry->encoded)) {
                                    $description .= '<br>' . html_entity_decode($entry->encoded);
                                } else if (isset($entry->content)) {
                                    $description .= '<br>' . html_entity_decode($entry->content);
                                }
                                $output .= '<p><a href="'.htmlspecialchars($entry->link).'" title="'.$date.'">'. $title .'</a> <small>(' . $date . ($time ? ' ' . $time : '') . ')</small></p>';
                                if ($image) {
                                    $output .= '<div><img src="'.$image.'" alt="Event-Bild" style="max-width:120px;max-height:120px;" /></div>';
                                }
                                $output .= '<div>'. $description .'</div>';
                            }
                        }

                        echo $output;
                        //file_put_contents($feedcache_path, $output);
                    } else {
                        echo file_get_contents($feedcache_path);
                    }
                    ?>
                <?php } ?>
            </div>
        </fieldset>
    </div>
<?php } ?>

    <!-- Skript für Slideshow -->
    <script>
        var slideIndex = 0;
        var slides = document.getElementsByClassName("PictureSlider");

        function carousel() {
            for (var i = 0; i < slides.length; i++) {
                slides[i].classList.remove("active");
            }
            slideIndex++;
            if (slideIndex > slides.length) { slideIndex = 1; }
            slides[slideIndex - 1].classList.add("active");
            setTimeout(carousel, <?php echo SLIDESHOW_DELAY; ?>);
        }

        document.addEventListener("DOMContentLoaded", function () {
            if (slides.length > 0) {
                slides[0].classList.add("active");
            }
            setTimeout(carousel, <?php echo SLIDESHOW_DELAY; ?>);
        });
    </script>

</bodysplash>
</html>
