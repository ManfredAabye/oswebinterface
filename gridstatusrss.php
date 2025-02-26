<?php
$title = "GridStatusRSS";
include_once 'include/header.php';

// Cache-Dateipfad
$feedcache_path = __DIR__.'/feed_cache.html';
$feedcache_max_age = 1800; // Cache max. 30 Minuten alt

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
        $output .= '<p><a href="' . htmlspecialchars($xml->channel->link) . '">Feed öffnen</a></p>';
        $output .= '<ul>';

        $counter = 0;
        foreach ($xml->channel->item as $entry) {
            if (++$counter > $max_entries) break;

            $date = date('d.m.Y', strtotime($entry->pubDate));
            $output .= '<li><a href="'.htmlspecialchars($entry->link).'" title="'.$date.'">'.htmlspecialchars($entry->title).'</a> <small>('.$date.')</small></li>';
        }

        $output .= '</ul>';
    }

    echo $output;
    //file_put_contents($feedcache_path, $output);
} else {
    echo file_get_contents($feedcache_path);
}
?>
<br><br><br><br>
