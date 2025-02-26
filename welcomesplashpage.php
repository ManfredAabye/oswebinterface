<?php
$title = "Welcome";
include_once "include/config.php";
?>

<html>

<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">

    <style>
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
            $sql = "SELECT regionName, serverIP, serverPort FROM regions ORDER BY last_seen DESC LIMIT 10";
            $resultregions = mysqli_query($con, $sql);

            while ($dsatz = mysqli_fetch_assoc($resultregions)) {
                $region = htmlspecialchars($dsatz["regionName"]);
                $ip = htmlspecialchars($dsatz["serverIP"]);
                $port = htmlspecialchars($dsatz["serverPort"]);

                // Standard-Koordinaten für den Teleport-Link (X=103, Y=113, Z=23)
                $regionslink = "hop://$ip:$port/$region/103/113/23";

                echo "<a class='region-link' href='$regionslink' target='_blank'>$region</a>";
            }

            mysqli_close($con);
            ?>
        </fieldset>
    </div>

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
