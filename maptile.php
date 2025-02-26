<?php
$title = "MapTile";
include_once 'include/header.php';

// Verbindung zur Datenbank herstellen
$con = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$con) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . mysqli_connect_error());
}

// Koordinaten des Mittelpunkts anpassen
$centerX = CONF_CENTER_COORD_X;
$centerY = CONF_CENTER_COORD_Y;

if ($centerY <= 30) {$centerY = 100;}
if ($centerX <= 30) {$centerX = 100;}
if ($centerX >= 99999) {$centerX = CONF_CENTER_COORD_X;}
if ($centerY >= 99999) {$centerY = CONF_CENTER_COORD_Y;}

$startX = $centerX - floor(MAPS_X / 2);
$startY = $centerY - floor(MAPS_Y / 2);

$endX = $centerX + floor(MAPS_X / 2);
$endY = $centerY + floor(MAPS_Y / 2);

function uuid4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// Datenbankabfrage, um alle Regionen zu erhalten
$regions = mysqli_query($con, "SELECT uuid, regionName, locX, locY, serverURI, sizeX, sizeY, owner_uuid FROM regions") or die("Error: " . mysqli_error($con));

$grid = [];
while ($row = mysqli_fetch_assoc($regions)) {
    $locX = $row['locX'] / 256;
    $locY = $row['locY'] / 256;
    $sizeX = $row['sizeX'];
    $sizeY = $row['sizeY'];

    if ($locX >= $startX && $locX <= $endX && $locY >= $startY && $locY <= $endY) {
        // Bestimme die Farbe basierend auf der Regionengröße
        if ($locX == $centerX && $locY == $centerY) {
            $color = CENTER_COLOR; // Rot markiert für das Zentrum
        } elseif ($sizeX == 256 && $sizeY == 256) {
            $color = BESCHLAGT_COLOR;
        } elseif ($sizeX > 256 || $sizeY > 256) {
            $color = VARREGION_COLOR;
        } else {
            $color = FREI_COLOR;
        }

        $grid[$locX][$locY] = [
            'color' => $color,
            'regionName' => $row['regionName'],
            'sizeX' => $sizeX,
            'sizeY' => $sizeY,
            'uuid' => $row['uuid'],
            'serverURI' => $row['serverURI'],
            'owner_uuid' => $row['owner_uuid']
        ];
    }
}
?>

<style>
.card {
    display: none;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    background-color: white;
    border: 1px solid #ccc;
    z-index: 10;
    color: black; /* Schriftfarbe auf Schwarz setzen */
}
.card.active {
    display: block;
}
</style>

<main>
    <h2><?php echo SITE_NAME; ?> MapTile Overview</h2>
    <div class="map-container" style="display: grid; grid-template-columns: repeat(<?php echo MAPS_X; ?>, <?php echo TILE_SIZE; ?>); gap: 1px;">
        <?php
        for ($x = $startX; $x < $startX + MAPS_X; $x++) {
            for ($y = $startY; $y < $startY + MAPS_Y; $y++) {
                $tile = isset($grid[$x][$y]) ? $grid[$x][$y] : ['color' => FREI_COLOR, 'regionName' => 'Free', 'sizeX' => '', 'sizeY' => ''];
                $tooltip = ($tile['regionName'] !== 'Free') ? "Koordinaten: ($x, $y), Region: {$tile['regionName']}, Größe: {$tile['sizeX']}x{$tile['sizeY']}" : "Koordinaten: ($x, $y), Region: Free";
                $clickAction = ($tile['regionName'] === 'Free') ? "showFreeRegionCard(event, $x, $y)" : "showOccupiedRegionCard(event, '{$tile['regionName']}', '{$tile['uuid']}', '{$tile['sizeX']}', '{$tile['sizeY']}', '{$tile['serverURI']}', '{$tile['owner_uuid']}')";
                echo "<div class='map-tile' title='$tooltip' style='width: " . TILE_SIZE . "; height: " . TILE_SIZE . "; background-color: {$tile['color']};' onclick=\"$clickAction\"></div>";
            }
        }
        ?>
    </div>

    <div id="freeRegionCard" class="card">
        <p>Diese Region ist frei und Sie können die Konfigurationen vornehmen.</p>
        <p id="free-region-coordinates"></p>
        <p>____________Beispiel_____________</p>
        <p>[Region_<span id="free-region-x"></span>_<span id="free-region-y"></span>]</p>
        <p>Location: <span id="free-region-x-location"></span>,<span id="free-region-y-location"></span></p>
        <p>RegionUUID: <?php echo uuid4(); ?></p>
        <p>SizeX: 256</p>
        <p>SizeY: 256</p>
        <p>SizeZ: 256</p>
        <p>InternalAddress: 0.0.0.0</p>
        <p>InternalPort: <?php echo rand(9000, 9250); ?></p>
        <p>ResolveAddress: False</p>
        <p>ExternalHostName: SYSTEMIP</p>
        <p>MaptileStaticUUID: <?php echo uuid4(); ?></p>
        <button onclick="hideCard()">Schließen</button>
    </div>

    <div id="occupiedRegionCard" class="card">
        <p>Region Name: <span id="region-name"></span></p>
        <p>Region UUID: <span id="region-uuid"></span></p>
        <p>Size: <span id="region-size"></span></p>
        <p>Server URI: <span id="server-uri"></span></p>
        <p>Owner UUID: <span id="owner-uuid"></span></p>
        <button onclick="hideCard()">Schließen</button>
    </div>
</main>

<script>
function showFreeRegionCard(event, x, y) {
    var card = document.getElementById('freeRegionCard');
    card.querySelector('#free-region-coordinates').innerText = "Koordinaten: (" + x + ", " + y + ")";
    card.querySelector('#free-region-x').innerText = x;
    card.querySelector('#free-region-y').innerText = y;
    card.querySelector('#free-region-x-location').innerText = x;
    card.querySelector('#free-region-y-location').innerText = y;
    card.style.top = event.clientY + 'px';
    card.style.left = event.clientX + 'px';
    card.classList.add('active');
}

function showOccupiedRegionCard(event, regionName, uuid, sizeX, sizeY, serverURI, ownerUUID) {
    var card = document.getElementById('occupiedRegionCard');
    card.querySelector('#region-name').innerText = regionName;
    card.querySelector('#region-uuid').innerText = uuid;
    card.querySelector('#region-size').innerText = sizeX + "x" + sizeY;
    card.querySelector('#server-uri').innerText = serverURI;
    card.querySelector('#owner-uuid').innerText = ownerUUID;
    card.style.top = event.clientY + 'px';
    card.style.left = event.clientX + 'px';
    card.classList.add('active');
}

function hideCard() {
    var cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.classList.remove('active');
    });
}
</script>
