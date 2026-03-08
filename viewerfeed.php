<?php
// Firestorm/OpenSim Web Profile bridge endpoint.
// Accepts viewer style query params and renders a profile feed page from OpenSim DB.

declare(strict_types=1);

include_once __DIR__ . '/include/env.php';

header('Content-Type: text/html; charset=UTF-8');
header_remove('X-Frame-Options');
header('Content-Security-Policy: frame-ancestors *');
header('Referrer-Policy: no-referrer-when-downgrade');

function h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function parse_bool(string $value): bool
{
    $v = strtolower(trim($value));
    return $v === '1' || $v === 'true' || $v === 'yes' || $v === 'on';
}

function connect_main_db(): mysqli
{
    $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($db->connect_errno) {
        http_response_code(500);
        echo '<h1>Database connection failed.</h1>';
        exit;
    }

    $db->set_charset('utf8mb4');
    return $db;
}

function is_uuid(string $value): bool
{
    return preg_match('/^[0-9a-fA-F-]{36}$/', $value) === 1;
}

function normalize_lookup_input(string $value): string
{
    $v = trim(urldecode($value));

    // Some viewers/grids may pass values wrapped in brackets, e.g. [uuid].
    if (strlen($v) >= 2 && $v[0] === '[' && $v[strlen($v) - 1] === ']') {
        $v = substr($v, 1, -1);
        $v = trim($v);
    }

    return $v;
}

function resolve_user_uuid(mysqli $db, string $nameOrUuid): ?array
{
    $input = normalize_lookup_input($nameOrUuid);
    if ($input === '') {
        return null;
    }

    if (is_uuid($input)) {
        $stmt = $db->prepare('SELECT PrincipalID, FirstName, LastName FROM UserAccounts WHERE PrincipalID = ? LIMIT 1');
        if ($stmt !== false) {
            $stmt->bind_param('s', $input);
            $stmt->execute();
            $stmt->bind_result($uuid, $first, $last);
            $found = $stmt->fetch();
            $stmt->close();
            if ($found) {
                return [
                    'uuid' => (string)$uuid,
                    'name' => trim((string)$first . ' ' . (string)$last),
                ];
            }
        }

        // UUID was passed by viewer but account row could not be resolved.
        // Continue with UUID so profile/picks queries can still work.
        return [
            'uuid' => $input,
            'name' => $input,
        ];
    }

    $normalized = strtolower(str_replace(' ', '.', $input));

    $sql = 'SELECT PrincipalID, FirstName, LastName
            FROM UserAccounts
            WHERE LOWER(CONCAT(FirstName, ".", LastName)) = ?
               OR LOWER(CONCAT(FirstName, " ", LastName)) = ?
            LIMIT 1';

    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        return null;
    }
    $stmt->bind_param('ss', $normalized, $normalized);
    $stmt->execute();
    $stmt->bind_result($uuid, $first, $last);
    $found = $stmt->fetch();
    $stmt->close();

    if (!$found) {
        return null;
    }

    return [
        'uuid' => (string)$uuid,
        'name' => trim((string)$first . ' ' . (string)$last),
    ];
}

function pick_lookup_value(): string
{
    // Prefer explicit UUID-style parameters when available.
    $uuidParam = normalize_lookup_input((string)($_GET['uuid'] ?? ''));
    $avatarIdParam = normalize_lookup_input((string)($_GET['avatar_id'] ?? ''));
    $idParam = normalize_lookup_input((string)($_GET['id'] ?? ''));
    $nameParam = normalize_lookup_input((string)($_GET['name'] ?? ''));
    $userParam = normalize_lookup_input((string)($_GET['user'] ?? ''));

    foreach ([$uuidParam, $avatarIdParam, $idParam] as $candidate) {
        if ($candidate !== '' && is_uuid($candidate)) {
            return $candidate;
        }
    }

    if ($nameParam !== '') {
        return $nameParam;
    }

    if ($userParam !== '') {
        return $userParam;
    }

    foreach ([$uuidParam, $avatarIdParam, $idParam] as $candidate) {
        if ($candidate !== '') {
            return $candidate;
        }
    }

    return '';
}

function load_user_profile(mysqli $db, string $uuid): ?array
{
    $stmt = $db->prepare('SELECT * FROM userprofile WHERE useruuid = ? LIMIT 1');
    if ($stmt === false) {
        return null;
    }

    $stmt->bind_param('s', $uuid);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $profile ?: null;
}

function load_user_picks(mysqli $db, string $uuid): array
{
    $sql = 'SELECT pickuuid, name, description, simname, posglobal, snapshotuuid, gatekeeper, enabled, sortorder
            FROM userpicks
            WHERE creatoruuid = ?
            ORDER BY sortorder ASC, name ASC';

    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        return [];
    }

    $stmt->bind_param('s', $uuid);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    $stmt->close();
    return $rows;
}

$feedOnly = parse_bool((string)($_GET['feed_only'] ?? '0'));
$diag = parse_bool((string)($_GET['diag'] ?? '0'));
$lookup = pick_lookup_value();

$db = connect_main_db();
$user = $lookup !== '' ? resolve_user_uuid($db, $lookup) : null;
$profile = null;
$picks = [];

if ($user !== null) {
    $profile = load_user_profile($db, $user['uuid']);
    $picks = load_user_picks($db, $user['uuid']);
}

$db->close();

$pageTitle = $user !== null ? ($user['name'] . ' - Viewer Feed') : 'Viewer Feed Bridge';

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title><?php echo h($pageTitle); ?></title>
    <style>
        :root {
            --bg: #f4f1ea;
            --panel: #ffffff;
            --ink: #1f2a30;
            --muted: #5b6a72;
            --line: #d6ddd8;
            --accent: #1d6a73;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            --bg-grad: #e4efe8;
            --pill-bg: #f8fbf9;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                /* Neutral slate palette close to Firestorm dark UI. */
                --bg: #1f2328;
                --panel: #2a2f36;
                --ink: #e6ebf0;
                --muted: #aeb8c2;
                --line: #414853;
                --accent: #7eb5d6;
                --shadow: 0 8px 18px rgba(0, 0, 0, 0.35);
                --bg-grad: #2c333b;
                --pill-bg: #343b45;
            }
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: radial-gradient(circle at top right, var(--bg-grad), var(--bg) 46%);
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, sans-serif;
            line-height: 1.45;
        }

        .wrap {
            max-width: 980px;
            margin: 0 auto;
            padding: 22px 14px 42px;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: var(--shadow);
        }

        h1, h2, h3 {
            margin: 0 0 8px;
            line-height: 1.2;
        }

        .meta { color: var(--muted); font-size: 0.93rem; }
        .hint { color: var(--muted); }
        .pill {
            display: inline-block;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 2px 10px;
            margin-right: 8px;
            font-size: 0.82rem;
            color: var(--muted);
            background: var(--pill-bg);
        }

        .pick {
            border-top: 1px dashed var(--line);
            padding-top: 10px;
            margin-top: 10px;
        }

        .pick:first-child {
            border-top: 0;
            padding-top: 0;
            margin-top: 0;
        }

        .desc {
            white-space: pre-wrap;
            margin-top: 6px;
        }

        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }

        @media (max-width: 700px) {
            .wrap { padding: 12px 10px 24px; }
            .card { padding: 12px; border-radius: 10px; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>OpenSim Viewer Feed Bridge</h1>
        <p class="meta">Use with Firestorm Web Profile URL, for example:<br><code>/oswebinterface/viewerfeed.php?name=[AGENT_NAME]</code></p>

<?php if ($lookup === ''): ?>
        <p class="hint">No avatar provided. Add <code>?name=first.last</code> or <code>?uuid=...</code>.</p>
<?php elseif ($user === null): ?>
        <p class="hint">Avatar not found for: <code><?php echo h($lookup); ?></code></p>
<?php else: ?>
        <h2><?php echo h($user['name']); ?></h2>
        <p class="meta">UUID: <code><?php echo h($user['uuid']); ?></code></p>
<?php endif; ?>

    <?php if ($diag): ?>
        <hr>
        <p class="meta"><strong>Diagnostics</strong></p>
        <p class="meta">raw query: <code><?php echo h((string)($_SERVER['QUERY_STRING'] ?? '')); ?></code></p>
        <p class="meta">remote addr: <code><?php echo h((string)($_SERVER['REMOTE_ADDR'] ?? '')); ?></code></p>
        <p class="meta">user-agent: <code><?php echo h((string)($_SERVER['HTTP_USER_AGENT'] ?? '')); ?></code></p>
    <?php endif; ?>
    </div>

<?php if ($user !== null): ?>
    <?php if (!$feedOnly && $profile !== null): ?>
    <div class="card">
        <h3>Profile</h3>
        <p><strong>About:</strong><br><?php echo nl2br(h((string)($profile['profileAboutText'] ?? ''))); ?></p>
        <p><strong>First Life:</strong><br><?php echo nl2br(h((string)($profile['profileFirstText'] ?? ''))); ?></p>
        <p><strong>Profile URL:</strong> <?php echo h((string)($profile['profileURL'] ?? '')); ?></p>
    </div>
    <?php elseif (!$feedOnly): ?>
    <div class="card">
        <p class="hint">No userprofile row found for this user.</p>
    </div>
    <?php endif; ?>

    <div class="card">
        <h3>Picks (<?php echo count($picks); ?>)</h3>

<?php if (count($picks) === 0): ?>
        <p class="hint">No picks available.</p>
<?php else: ?>
        <?php foreach ($picks as $pick): ?>
        <div class="pick">
            <h3><?php echo h((string)($pick['name'] ?? '(untitled)')); ?></h3>
            <div>
                <span class="pill">sim: <?php echo h((string)($pick['simname'] ?? '')); ?></span>
                <span class="pill">enabled: <?php echo h((string)($pick['enabled'] ?? '')); ?></span>
                <span class="pill">sort: <?php echo h((string)($pick['sortorder'] ?? '')); ?></span>
            </div>
            <p class="desc"><?php echo nl2br(h((string)($pick['description'] ?? ''))); ?></p>
            <p class="meta">pickuuid: <code><?php echo h((string)($pick['pickuuid'] ?? '')); ?></code></p>
        </div>
        <?php endforeach; ?>
<?php endif; ?>
    </div>
<?php endif; ?>
</div>
</body>
</html>
