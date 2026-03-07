<?php
require_once __DIR__ . '/include/config.php';

/**
 * Decide whether caller expects JSON. Robust LoginService reads plain text,
 * while web tools can still request JSON.
 */
function wants_json_response(): bool
{
    if (isset($_GET['format']) && strtolower((string)$_GET['format']) === 'json') {
        return true;
    }

    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    return stripos($accept, 'application/json') !== false;
}

/**
 * Query a single numeric value and return null on failure.
 */
function query_scalar_int(mysqli $conn, string $sql): ?int
{
    $result = $conn->query($sql);
    if ($result === false) {
        return null;
    }

    $row = $result->fetch_row();
    $result->free();

    if (!$row || !isset($row[0])) {
        return null;
    }

    return (int)$row[0];
}

/**
 * Read live grid stats from the Robust database.
 */
function load_grid_stats(): ?array
{
    if (!defined('DB_SERVER') || !defined('DB_USERNAME') || !defined('DB_PASSWORD') || !defined('DB_NAME')) {
        return null;
    }

    $conn = @new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_errno) {
        return null;
    }

    $stats = [
        'online_users' => query_scalar_int($conn, 'SELECT COUNT(*) FROM Presence'),
        'regions' => query_scalar_int($conn, 'SELECT COUNT(*) FROM regions'),
        'accounts' => query_scalar_int($conn, 'SELECT COUNT(*) FROM UserAccounts'),
        'active_30d' => query_scalar_int($conn, 'SELECT COUNT(*) FROM GridUser WHERE Login > (UNIX_TIMESTAMP() - (30*86400))'),
        'grid_users' => query_scalar_int($conn, 'SELECT COUNT(*) FROM GridUser'),
    ];

    $conn->close();

    foreach ($stats as $value) {
        if ($value !== null) {
            return $stats;
        }
    }

    return null;
}

/**
 * Build MOTD text for viewer login.
 */
function build_message_text(?array $stats): string
{
    if (defined('MOTD') && MOTD !== 'Dyn') {
        return defined('MOTD_STATIC_MESSAGE') ? (string)MOTD_STATIC_MESSAGE : 'Welcome to the grid!';
    }

    $hour = (int)date('G');
    $greeting = $hour < 12 ? 'Good morning' : 'Good day';
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'to the Grid';
    $text = $greeting . ' on ' . $siteName . '!';

    if ($stats !== null) {
        $text .= sprintf(
            ' Online: %d - Regions: %d - Active 30 days: %d',
            (int)($stats['online_users'] ?? 0),
            (int)($stats['regions'] ?? 0),
            (int)($stats['active_30d'] ?? 0)
        );
    }

    return $text;
}

$stats = load_grid_stats();
$messageText = build_message_text($stats);

$payload = [
    'message' => $messageText,
    'type' => defined('MOTD_STATIC_TYPE') ? MOTD_STATIC_TYPE : 'system',
    'url_tos' => defined('MOTD_STATIC_URL_TOS') ? MOTD_STATIC_URL_TOS : (BASE_URL . '/include/tos.php'),
    'url_dmca' => defined('MOTD_STATIC_URL_DMCA') ? MOTD_STATIC_URL_DMCA : (BASE_URL . '/include/dmca.php'),
    'stats' => $stats,
    'generated_at' => gmdate('c'),
];

if (wants_json_response()) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

header('Content-Type: text/plain; charset=utf-8');
echo $messageText;