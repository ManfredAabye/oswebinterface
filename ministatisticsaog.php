<?php
$title = 'Mini Statistics Active OpenSim Grids';
require_once __DIR__ . '/include/config.php';

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

function normalize_grid_uri($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    $value = preg_replace('/^hop:\/\//i', '', $value);
    $value = preg_replace('/^https?:\/\//i', '', $value);
    $value = trim($value, " \t\n\r\0\x0B/");

    return strtolower($value);
}

function sanitize_grid_uri($value)
{
    return normalize_grid_uri($value);
}

function parse_grid_user_login_uri($rawUserId)
{
    $parts = explode(';', (string) $rawUserId);
    if (count($parts) < 2) {
        return '';
    }

    return sanitize_grid_uri($parts[1]);
}

function parse_hg_address($serverUrl)
{
    $serverUrl = trim((string) $serverUrl);
    if ($serverUrl === '') {
        return '';
    }

    if (preg_match('/^https?:\/\//i', $serverUrl) === 1) {
        $parsed = parse_url($serverUrl);
        if (!empty($parsed['host'])) {
            $host = strtolower($parsed['host']);
            $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
            return sanitize_grid_uri($host . $port);
        }
    }

    return sanitize_grid_uri($serverUrl);
}

function derive_grid_name($uri)
{
    $uri = normalize_grid_uri($uri);
    if ($uri === '') {
        return 'Unknown Grid';
    }

    $host = explode(':', $uri)[0];
    $host = preg_replace('/^www\./i', '', $host);
    $parts = explode('.', $host);
    $name = $parts[0] ?? $host;

    if ($name === '') {
        return 'Unknown Grid';
    }

    return ucwords(str_replace(array('-', '_'), ' ', $name));
}

function ensure_http_scheme($uri)
{
    $uri = trim((string) $uri);
    if ($uri === '') {
        return '';
    }

    if (preg_match('/^https?:\/\//i', $uri) === 1) {
        return $uri;
    }

    return 'http://' . $uri;
}

function host_from_uri($uri)
{
    $uri = normalize_grid_uri($uri);
    if ($uri === '') {
        return '';
    }

    $candidate = ensure_http_scheme($uri);
    $parts = parse_url($candidate);
    if (!empty($parts['host'])) {
        return strtolower($parts['host']);
    }

    $host = explode(':', $uri)[0];
    return strtolower(trim($host));
}

function is_hg_address_uri($uri)
{
    $host = host_from_uri($uri);
    return $host !== '' && strpos($host, 'hg.') === 0;
}

function build_grid_info_urls($loginUri)
{
    $loginUri = sanitize_grid_uri($loginUri);
    if ($loginUri === '') {
        return array();
    }

    $baseHttp = ensure_http_scheme($loginUri);
    $baseHttps = preg_replace('/^http:\/\//i', 'https://', $baseHttp);

    $urls = array(
        rtrim($baseHttp, '/') . '/get_grid_info',
        rtrim($baseHttps, '/') . '/get_grid_info',
    );

    $parts = parse_url($baseHttp);
    if (!empty($parts['host'])) {
        $hostPort = strtolower($parts['host']);
        if (isset($parts['port'])) {
            $hostPort .= ':' . $parts['port'];
        }

        $urls[] = 'http://' . $hostPort . '/get_grid_info';
        $urls[] = 'https://' . $hostPort . '/get_grid_info';

        $path = isset($parts['path']) ? trim((string) $parts['path']) : '';
        if ($path !== '' && $path !== '/') {
            $baseDir = dirname($path);
            if ($baseDir === '\\' || $baseDir === '.') {
                $baseDir = '';
            }
            $baseDir = trim((string) $baseDir);
            if ($baseDir !== '' && $baseDir !== '/') {
                $baseDir = '/' . trim($baseDir, '/');
                $urls[] = 'http://' . $hostPort . $baseDir . '/get_grid_info';
                $urls[] = 'https://' . $hostPort . $baseDir . '/get_grid_info';
            }
        }
    }

    return array_values(array_unique($urls));
}

function parse_grid_info_payload($payload)
{
    $payload = trim((string) $payload);
    if ($payload === '') {
        return array();
    }

    $json = json_decode($payload, true);
    if (is_array($json) && !empty($json)) {
        return $json;
    }

    if (strpos($payload, '<') !== false && strpos($payload, '>') !== false) {
        $xml = @simplexml_load_string($payload);
        if ($xml !== false) {
            $data = array();
            foreach ($xml->children() as $key => $value) {
                $k = strtolower(trim((string) $key));
                if ($k !== '') {
                    $data[$k] = trim((string) $value);
                }
            }
            if (!empty($data)) {
                return $data;
            }
        }
    }

    $data = array();
    $lines = preg_split('/\r\n|\r|\n/', $payload);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '=') === false) {
            continue;
        }

        list($key, $value) = array_map('trim', explode('=', $line, 2));
        if ($key !== '') {
            $data[strtolower($key)] = $value;
        }
    }

    return $data;
}

function fetch_public_grid_info($loginUri)
{
    $urls = build_grid_info_urls($loginUri);
    if (empty($urls)) {
        return array();
    }

    foreach ($urls as $url) {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 1.5,
                'ignore_errors' => true,
                'header' => "User-Agent: oswebinterface-gridinfo/1.0\r\n",
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
            ),
        ));

        $payload = @file_get_contents($url, false, $context);
        if ($payload === false) {
            continue;
        }

        $parsed = parse_grid_info_payload($payload);
        if (!empty($parsed)) {
            $parsed['_source_url'] = $url;
            return $parsed;
        }
    }

    return array();
}

function read_grid_info_cache($cacheFile)
{
    if (!is_readable($cacheFile)) {
        return array();
    }

    $raw = @file_get_contents($cacheFile);
    if ($raw === false || $raw === '') {
        return array();
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : array();
}

function write_grid_info_cache($cacheFile, $cacheData)
{
    @file_put_contents($cacheFile, json_encode($cacheData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function find_grid_info_value($gridInfo, $keys)
{
    foreach ($keys as $key) {
        $lower = strtolower($key);
        if (isset($gridInfo[$lower]) && trim((string) $gridInfo[$lower]) !== '') {
            return trim((string) $gridInfo[$lower]);
        }
        if (isset($gridInfo[$key]) && trim((string) $gridInfo[$key]) !== '') {
            return trim((string) $gridInfo[$key]);
        }
    }

    return '';
}

function viewer_default_login_page($gridUri, $loginUri)
{
    $gridHost = host_from_uri($gridUri);
    if ($gridHost === '') {
        $gridHost = host_from_uri($loginUri);
    }

    if ($gridHost === '') {
        return '';
    }

    return 'http://' . $gridHost . '/';
}

function csv_output_and_exit($rows)
{
    $filename = 'mini-active-opensim-grids-' . date('Ymd-His') . '.csv';

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $out = fopen('php://output', 'w');
    if ($out === false) {
        http_response_code(500);
        echo 'CSV export failed.';
        exit;
    }

    fputcsv($out, array('Grid Name', 'Grid-URI', 'Login-Seite', 'LoginURI', 'HG-address', 'GridUser Count', 'UserInfo Count', 'Total'));

    foreach ($rows as $row) {
        fputcsv($out, array(
            $row['grid_name'],
            $row['grid_uri'],
            $row['login_page'],
            $row['login_uri'],
            $row['hg_address'],
            $row['griduser_count'],
            $row['userinfo_count'],
            $row['total_count'],
        ));
    }

    fclose($out);
    exit;
}

$extendedCsvFile = __DIR__ . '/include/minigroundgridlist.csv';
$gridInfoCacheFile = __DIR__ . '/include/minigroundgridinfo_cache.json';
$gridInfoCacheTtl = 43200;
$completeRefreshMode = true;

$mysqli = @mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli === false) {
    http_response_code(500);
    echo 'Datenbankverbindung fehlgeschlagen.';
    exit;
}

$gridMap = array();

$gridUserQuery = mysqli_query($mysqli, 'SELECT * FROM `GridUser` ORDER BY `GridUser`.`UserID` DESC');
if ($gridUserQuery !== false) {
    while ($row = mysqli_fetch_assoc($gridUserQuery)) {
        $userId = isset($row['UserID']) ? $row['UserID'] : '';
        $loginUri = parse_grid_user_login_uri($userId);
        if ($loginUri === '') {
            continue;
        }

        if (!isset($gridMap[$loginUri])) {
            $gridMap[$loginUri] = array(
                'grid_name' => derive_grid_name($loginUri),
                'grid_uri' => '',
                'login_page' => '',
                'login_uri' => $loginUri,
                'hg_address' => $loginUri,
                'griduser_count' => 0,
                'userinfo_count' => 0,
            );
        }

        $gridMap[$loginUri]['griduser_count']++;
    }
    mysqli_free_result($gridUserQuery);
}

$userInfoQuery = mysqli_query($mysqli, 'SELECT * FROM `userinfo` ORDER BY `userinfo`.`serverurl` ASC');
if ($userInfoQuery !== false) {
    while ($row = mysqli_fetch_assoc($userInfoQuery)) {
        $hgAddress = isset($row['serverurl']) ? parse_hg_address($row['serverurl']) : '';
        if ($hgAddress === '') {
            continue;
        }

        if (!isset($gridMap[$hgAddress])) {
            $gridMap[$hgAddress] = array(
                'grid_name' => derive_grid_name($hgAddress),
                'grid_uri' => '',
                'login_page' => '',
                'login_uri' => '',
                'hg_address' => $hgAddress,
                'griduser_count' => 0,
                'userinfo_count' => 0,
            );
        }

        $gridMap[$hgAddress]['userinfo_count']++;
        $gridMap[$hgAddress]['hg_address'] = $hgAddress;
    }
    mysqli_free_result($userInfoQuery);
}

mysqli_close($mysqli);

$gridInfoCache = read_grid_info_cache($gridInfoCacheFile);
$cacheChanged = false;
$now = time();
$refreshCount = 0;
$rowsChanged = false;

$rows = array_values($gridMap);
foreach ($rows as &$row) {
    $queryUri = $row['login_uri'] !== '' ? $row['login_uri'] : $row['hg_address'];
    $cacheKey = sanitize_grid_uri($queryUri);

    if ($cacheKey === '') {
        $row['total_count'] = (int) $row['griduser_count'] + (int) $row['userinfo_count'];
        continue;
    }

    $gridInfo = array();
    if (isset($gridInfoCache[$cacheKey]['data']) && is_array($gridInfoCache[$cacheKey]['data'])) {
        $gridInfo = $gridInfoCache[$cacheKey]['data'];
    }

    if ($completeRefreshMode || empty($gridInfo)) {
        $refreshCount++;
        $gridInfo = fetch_public_grid_info($queryUri);
        $gridInfoCache[$cacheKey] = array(
            'fetched_at' => $now,
            'data' => $gridInfo,
        );
        $cacheChanged = true;
    }

    if (!empty($gridInfo)) {
        $publicName = find_grid_info_value($gridInfo, array('gridname', 'gridnick', 'label'));
        if ($publicName !== '') {
            if ($row['grid_name'] !== $publicName) {
                $rowsChanged = true;
            }
            $row['grid_name'] = $publicName;
        }

        $publicGridUri = find_grid_info_value($gridInfo, array('name', 'griduri', 'grid_uri', 'gridurl', 'grid_url', 'gridnick'));
        if ($publicGridUri !== '') {
            if ($row['grid_uri'] !== $publicGridUri) {
                $rowsChanged = true;
                $row['grid_uri'] = $publicGridUri;
            }
        }

        $publicLoginPage = find_grid_info_value($gridInfo, array('loginpage', 'login_page'));
        if ($publicLoginPage !== '') {
            if ($row['login_page'] !== $publicLoginPage) {
                $rowsChanged = true;
                $row['login_page'] = $publicLoginPage;
            }
        }

        $publicLogin = find_grid_info_value($gridInfo, array('login', 'loginuri', 'login_uri'));
        if ($publicLogin !== '') {
            $newLogin = sanitize_grid_uri($publicLogin);
            if ($newLogin !== '') {
                if (is_hg_address_uri($newLogin)) {
                    $row['hg_address'] = $newLogin;
                } else {
                    $row['login_uri'] = $newLogin;
                }
                $rowsChanged = true;
            }
        }
    }

    if ($row['login_page'] === '') {
        $fallbackLoginPage = viewer_default_login_page($row['grid_uri'], $row['login_uri']);
        if ($fallbackLoginPage !== '') {
            $row['login_page'] = $fallbackLoginPage;
            $rowsChanged = true;
        }
    }

    $row['total_count'] = (int) $row['griduser_count'] + (int) $row['userinfo_count'];
}
unset($row);

if ($cacheChanged) {
    write_grid_info_cache($gridInfoCacheFile, $gridInfoCache);
}

usort($rows, function ($a, $b) {
    if ($a['total_count'] === $b['total_count']) {
        return strcmp($a['grid_name'], $b['grid_name']);
    }
    return $b['total_count'] <=> $a['total_count'];
});

$needsCsvWrite = $rowsChanged || !is_file($extendedCsvFile);
if ($needsCsvWrite) {
    $writeHandle = @fopen($extendedCsvFile, 'w');
    if ($writeHandle !== false) {
        fputcsv($writeHandle, array('Grid Name', 'Grid-URI', 'Login-Seite', 'LoginURI', 'HG-address', 'GridUser Count', 'UserInfo Count', 'Total'));
        foreach ($rows as $row) {
            fputcsv($writeHandle, array(
                $row['grid_name'],
                $row['grid_uri'],
                $row['login_page'],
                $row['login_uri'],
                $row['hg_address'],
                $row['griduser_count'],
                $row['userinfo_count'],
                $row['total_count'],
            ));
        }
        fclose($writeHandle);
    }
}

if (isset($_GET['download']) && $_GET['download'] === '1') {
    csv_output_and_exit($rows);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Statistics Active OpenSim Grids</title>
    <style>
        :root {
            --panel: #ffffff;
            --text: #1b1f23;
            --muted: #586069;
            --accent: #006d77;
            --accent-strong: #00545b;
            --line: #d8dee4;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
            background: linear-gradient(180deg, #edf5f6 0%, #f9fbfb 100%);
            color: var(--text);
        }

        .wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 10px;
            box-shadow: 0 8px 26px rgba(20, 38, 42, 0.06);
            padding: 18px;
        }

        .toolbar {
            margin: 14px 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 9px 12px;
            background: var(--accent);
            color: #ffffff;
            text-decoration: none;
            border-radius: 7px;
            font-size: 14px;
        }

        .btn:hover {
            background: var(--accent-strong);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th,
        td {
            border-bottom: 1px solid var(--line);
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f0f5f6;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Mini Statistics Active OpenSim Grids</h1>
        <p>Nur Datenbankdaten (GridUser + userinfo), ohne include/gridlist.csv.</p>

        <div class="toolbar">
            <a class="btn" href="?download=1">CSV Download</a>
            <span>Datensaetze: <?php echo (int) count($rows); ?></span>
            <span>Exportdatei: include/minigroundgridlist.csv</span>
            <span>Grid-Info Refresh: <?php echo (int) $refreshCount; ?></span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Grid Name</th>
                        <th>Grid-URI</th>
                        <th>Login-Seite</th>
                        <th>LoginURI</th>
                        <th>HG-address</th>
                        <th class="num">GridUser</th>
                        <th class="num">UserInfo</th>
                        <th class="num">Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="9">Keine Daten vorhanden.</td>
                    </tr>
                <?php else: ?>
                    <?php $rank = 1; ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($row['grid_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['grid_uri']); ?></td>
                            <td><?php echo htmlspecialchars($row['login_page']); ?></td>
                            <td><?php echo htmlspecialchars($row['login_uri']); ?></td>
                            <td><?php echo htmlspecialchars($row['hg_address']); ?></td>
                            <td class="num"><?php echo (int) $row['griduser_count']; ?></td>
                            <td class="num"><?php echo (int) $row['userinfo_count']; ?></td>
                            <td class="num"><?php echo (int) $row['total_count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
