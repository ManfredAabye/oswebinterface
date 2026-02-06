<?php
header("Content-Type: application/rss+xml; charset=UTF-8");
include_once "config.php";

// Alle Kalenderdateien für die nächsten Jahre durchsuchen
$calendarDir = realpath(__DIR__ . '/../calendar');
$eventFiles = glob($calendarDir . '/events*.json');
$allEvents = [];

foreach ($eventFiles as $file) {
    $json = file_get_contents($file);
    if (!$json) continue;
    $data = json_decode($json, true);
    if (!is_array($data) || count($data) === 0) continue;
    // Prüfen, ob verschachteltes Format (date + events)
    $first = $data[0];
    if (is_array($first) && isset($first['date']) && isset($first['events']) && is_array($first['events'])) {
        foreach ($data as $day) {
            if (!isset($day['date']) || !is_array($day['events'])) continue;
            foreach ($day['events'] as $ev) {
                $ev['date'] = $day['date'];
                $allEvents[] = $ev;
            }
        }
    } else {
        // Altes Format (flach)
        foreach ($data as $ev) {
            if (!isset($ev['date'])) continue;
            $allEvents[] = $ev;
        }
    }
}

// Nach Datum und Uhrzeit sortieren
usort($allEvents, function($a, $b) {
    $cmp = strcmp($a['date'], $b['date']);
    if ($cmp !== 0) return $cmp;
    return strcmp($a['time'] ?? '', $b['time'] ?? '');
});

// Nächsten Termin bestimmen
$currentDate = date('Y-m-d');
$eventsToday = array_filter($allEvents, function($ev) use ($currentDate) {
    return $ev['date'] === $currentDate;
});
$nextEvent = null;
if (count($eventsToday) === 0) {
    foreach ($allEvents as $event) {
        if ($event['date'] >= $currentDate) {
            $nextEvent = $event;
            break;
        }
    }
}

// RSS-Feed beginnen
// Korrekte XML-Struktur: Header und <rss><channel>
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0">
    <channel>
        <title><?php echo CALENDAR_TITLE; ?></title>
        <link><?php echo BASE_URL; ?></link>
        <description>RSS-Feed mit Kalenderereignissen</description>
        <language>de-de</language>

        <?php if (count($eventsToday) > 0): ?>
            <?php foreach ($eventsToday as $ev): ?>
                <item>
                    <title>
                        <?php 
                            if (isset($ev['title'])) {
                                echo htmlspecialchars($ev['title']); 
                            } else if (isset($ev['texts'][0])) {
                                echo htmlspecialchars($ev['texts'][0]);
                            } else {
                                echo 'Kalenderereignis';
                            }
                        ?>
                    </title>
                    <link><?php echo htmlspecialchars($ev['link'] ?? ''); ?></link>
                    <description>
                        <?php 
                            if (isset($ev['description'])) {
                                echo htmlspecialchars($ev['description']); 
                            } else if (isset($ev['texts'][1])) {
                                echo htmlspecialchars(join(', ', array_slice($ev['texts'], 1)));
                            } else {
                                echo '';
                            }
                        ?>
                    </description>
                    <pubDate><?php echo date('r', strtotime($ev['date'] . (isset($ev['time']) ? ' ' . $ev['time'] : ''))); ?></pubDate>
                    <?php if (isset($ev['image'])): ?>
                        <enclosure url="<?php 
                            $imgPath = $ev['image'];
                            // Korrekte URL: Nur absolute oder relative Pfade
                            if (preg_match('#^https?://#', $imgPath)) {
                                echo htmlspecialchars($imgPath);
                            } else {
                                $base = dirname($_SERVER['SCRIPT_NAME']);
                                $base = preg_replace('#/include$#', '', $base);
                                $imgPath = '/' . ltrim($imgPath, '/');
                                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                                $host = $_SERVER['HTTP_HOST'];
                                echo htmlspecialchars($scheme . '://' . $host . $base . $imgPath);
                            }
                        ?>" type="image/jpeg" />
                    <?php endif; ?>
                </item>
            <?php endforeach; ?>
        <?php else: ?>
                <?php if ($nextEvent): ?>
                    <item>
                        <title>
                            <?php 
                                if (isset($nextEvent['title'])) {
                                    echo htmlspecialchars($nextEvent['title']); 
                                } else {
                                    echo htmlspecialchars($nextEvent['texts'][0]);
                                }
                            ?>
                        </title>
                        <link><?php echo htmlspecialchars($nextEvent['link']); ?></link>
                        <description>
                            <?php 
                                if (isset($nextEvent['description'])) {
                                    echo htmlspecialchars($nextEvent['description']); 
                                } else {
                                    echo htmlspecialchars(join(', ', array_slice($nextEvent['texts'], 1)));
                                }
                            ?>
                        </description>
                        <pubDate><?php echo date('r', strtotime($nextEvent['date'])); ?></pubDate>
                        <?php if (isset($nextEvent['image'])): ?>
                            <enclosure url="<?php 
                                $imgPath = $nextEvent['image'];
                                if (preg_match('#^https?://#', $imgPath)) {
                                    echo htmlspecialchars($imgPath);
                                } else {
                                    $base = dirname($_SERVER['SCRIPT_NAME']);
                                    $base = preg_replace('#/include$#', '', $base);
                                    $imgPath = '/' . ltrim($imgPath, '/');
                                    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                                    $host = $_SERVER['HTTP_HOST'];
                                    echo htmlspecialchars($scheme . '://' . $host . $base . $imgPath);
                                }
                            ?>" type="image/jpeg" />
                        <?php endif; ?>
                    </item>
                <?php endif; ?>
        <?php endif; ?>
    </channel>
</rss>
