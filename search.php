<?php
$title = "Web Search";
include 'include/header.php';

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');

function gs_param($keys, $default = '')
{
    foreach ($keys as $key) {
        if (isset($_GET[$key])) {
            return trim((string) $_GET[$key]);
        }
    }
    return $default;
}

function gs_escape_like($value)
{
    return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value);
}

function gs_table_exists($mysqli, $tableName)
{
    $sql = 'SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1';
    $stmt = mysqli_prepare($mysqli, $sql);
    if ($stmt === false) {
        return false;
    }
    mysqli_stmt_bind_param($stmt, 's', $tableName);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $exists = ($res !== false && mysqli_num_rows($res) > 0);
    if ($res !== false) {
        mysqli_free_result($res);
    }
    mysqli_stmt_close($stmt);
    return $exists;
}

$queryText = gs_param(array('q', 'query', 'search', 'term', 's', 'text'), '');
$queryType = strtolower(gs_param(array('type', 'category', 'scope', 't'), 'all'));

$typeAlias = array(
    'person' => 'people',
    'avatars' => 'people',
    'avatar' => 'people',
    'group' => 'groups',
    'place' => 'places',
    'regions' => 'places',
    'region' => 'places',
    'classified' => 'classifieds',
    'classifieds' => 'classifieds',
);
if (isset($typeAlias[$queryType])) {
    $queryType = $typeAlias[$queryType];
}

$allowedTypes = array('all', 'people', 'groups', 'places', 'classifieds');
if (!in_array($queryType, $allowedTypes, true)) {
    $queryType = 'all';
}

$mysqli = @mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli === false) {
    http_response_code(500);
    echo 'Datenbankverbindung fehlgeschlagen.';
    exit;
}

$rows = array();
$warnings = array();

if ($queryText !== '') {
    $needle = '%' . gs_escape_like($queryText) . '%';

    if ($queryType === 'all' || $queryType === 'people') {
        $sql = 'SELECT FirstName, LastName FROM UserAccounts WHERE FirstName LIKE ? OR LastName LIKE ? ORDER BY FirstName, LastName LIMIT 100';
        $stmt = mysqli_prepare($mysqli, $sql);
        if ($stmt !== false) {
            mysqli_stmt_bind_param($stmt, 'ss', $needle, $needle);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res !== false) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $rows[] = array(
                        'type' => 'People',
                        'name' => trim(($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? '')),
                        'extra' => '',
                    );
                }
                mysqli_free_result($res);
            }
            mysqli_stmt_close($stmt);
        }
    }

    if ($queryType === 'all' || $queryType === 'groups') {
        $sql = 'SELECT Name, Charter FROM os_groups_groups WHERE Name LIKE ? OR Charter LIKE ? ORDER BY Name LIMIT 100';
        $stmt = mysqli_prepare($mysqli, $sql);
        if ($stmt !== false) {
            mysqli_stmt_bind_param($stmt, 'ss', $needle, $needle);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res !== false) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $rows[] = array(
                        'type' => 'Groups',
                        'name' => (string) ($r['Name'] ?? ''),
                        'extra' => (string) ($r['Charter'] ?? ''),
                    );
                }
                mysqli_free_result($res);
            }
            mysqli_stmt_close($stmt);
        }
    }

    if ($queryType === 'all' || $queryType === 'places') {
        $sql = 'SELECT regionName, serverIP, serverPort FROM regions WHERE regionName LIKE ? ORDER BY regionName LIMIT 100';
        $stmt = mysqli_prepare($mysqli, $sql);
        if ($stmt !== false) {
            mysqli_stmt_bind_param($stmt, 's', $needle);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res !== false) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $extra = (string) ($r['serverIP'] ?? '');
                    if (isset($r['serverPort']) && $r['serverPort'] !== '') {
                        $extra .= ':' . $r['serverPort'];
                    }
                    $rows[] = array(
                        'type' => 'Places',
                        'name' => (string) ($r['regionName'] ?? ''),
                        'extra' => $extra,
                    );
                }
                mysqli_free_result($res);
            }
            mysqli_stmt_close($stmt);
        }
    }

    if ($queryType === 'all' || $queryType === 'classifieds') {
        if (gs_table_exists($mysqli, 'classifieds')) {
            $sql = 'SELECT name, simname, parcelname FROM classifieds WHERE name LIKE ? OR description LIKE ? OR simname LIKE ? ORDER BY expirationdate DESC LIMIT 100';
            $stmt = mysqli_prepare($mysqli, $sql);
            if ($stmt !== false) {
                mysqli_stmt_bind_param($stmt, 'sss', $needle, $needle, $needle);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res !== false) {
                    while ($r = mysqli_fetch_assoc($res)) {
                        $rows[] = array(
                            'type' => 'Classifieds',
                            'name' => (string) ($r['name'] ?? ''),
                            'extra' => trim((string) ($r['simname'] ?? '') . ' / ' . (string) ($r['parcelname'] ?? ''), ' /'),
                        );
                    }
                    mysqli_free_result($res);
                }
                mysqli_stmt_close($stmt);
            }
        } elseif ($queryType === 'classifieds') {
            $warnings[] = 'Classifieds table not found in this database.';
        }
    }

}

mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grid Search</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #151515;
            --bg-top: #2a2a2a;
            --panel: #1f1f1f;
            --panel-border: #3d3d3d;
            --text: #ececec;
            --muted: #b5b5b5;
            --input-bg: #242424;
            --input-border: #505050;
            --th-bg: #2d2d2d;
            --row-border: #3a3a3a;
            --accent: #7a7a7a;
            --accent-2: #5e5e5e;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 1rem;
            background: linear-gradient(180deg, var(--bg-top) 0%, var(--bg) 42%);
            color: var(--text);
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 8px;
            padding: 1rem;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            margin: 0 auto;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
        }

        .toolbar {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        input,
        select,
        button {
            padding: 0.45rem 0.55rem;
            border: 1px solid var(--input-border);
            border-radius: 6px;
            background: var(--input-bg);
            color: var(--text);
        }

        input::placeholder {
            color: var(--muted);
        }

        button {
            background: linear-gradient(180deg, var(--accent) 0%, var(--accent-2) 100%);
            color: #ffffff;
            border-color: #6c6c6c;
            cursor: pointer;
        }

        button:hover {
            filter: brightness(1.06);
        }

        input:focus,
        select:focus,
        button:focus {
            outline: 2px solid rgba(170, 170, 170, 0.35);
            outline-offset: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border-bottom: 1px solid var(--row-border);
            text-align: left;
            padding: 0.55rem;
            font-size: 0.95rem;
        }

        th {
            background: var(--th-bg);
        }

        tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.02);
        }

        .muted {
            color: var(--muted);
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Grid Search</h2>
        <form method="get" class="toolbar">
            <input type="text" name="q" value="<?php echo htmlspecialchars($queryText); ?>" placeholder="Search term..." required>
            <select name="type">
                <option value="all" <?php echo $queryType === 'all' ? 'selected' : ''; ?>>All</option>
                <option value="people" <?php echo $queryType === 'people' ? 'selected' : ''; ?>>People</option>
                <option value="groups" <?php echo $queryType === 'groups' ? 'selected' : ''; ?>>Groups</option>
                <option value="places" <?php echo $queryType === 'places' ? 'selected' : ''; ?>>Places</option>
                <option value="classifieds" <?php echo $queryType === 'classifieds' ? 'selected' : ''; ?>>Classifieds</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <?php if ($queryText === ''): ?>
            <p class="muted">Search parameter can be passed by viewer as `q`, `query`, `search` or `term`.</p>
        <?php else: ?>
            <p class="muted">Results: <?php echo count($rows); ?></p>
            <?php foreach ($warnings as $warning): ?>
                <p class="muted"><?php echo htmlspecialchars($warning); ?></p>
            <?php endforeach; ?>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Extra</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="3">No results found.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['extra']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
