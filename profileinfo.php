
<?php
// Dateiname: profileinfo.php
// Beschreibung: Zeigt ein OpenSim-Nutzerprofil basierend auf Benutzername oder UUID an.

/*
Fuer Bildvorschau mit JP2 bitte installieren:
sudo apt install imagemagick
sudo apt install php-intl
sudo apt install php-imagick
*/

include_once __DIR__ . '/include/env.php';

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function format_multiline(?string $value): string
{
    $clean = trim((string) $value);
    if ($clean === '') {
        return '<span class="empty">Keine Angabe</span>';
    }

    return nl2br(h($clean));
}

function format_single(?string $value): string
{
    $clean = trim((string) $value);
    return $clean === '' ? '<span class="empty">Keine Angabe</span>' : h($clean);
}

function bool_to_label(?string $value): string
{
    return ord((string) $value) ? 'Ja' : 'Nein';
}

function fetch_asset_blob(mysqli $conn, string $assetId): ?string
{
    $assetId = trim($assetId);
    if ($assetId === '') {
        return null;
    }

    $compact = strtolower(str_replace('-', '', $assetId));
    $variants = [$assetId];

    // OpenSim grids may store IDs with different UUID formatting.
    if ($compact !== '') {
        $variants[] = strtolower($assetId);
        $variants[] = strtoupper($assetId);
        $variants[] = $compact;
    }

    $variants = array_values(array_unique($variants));

    $tables = ['assets', 'assets_0'];

    foreach ($tables as $tableName) {
        foreach ($variants as $candidate) {
            $stmt = $conn->prepare("SELECT data FROM {$tableName} WHERE id=? LIMIT 1");
            if ($stmt === false) {
                continue;
            }

            $stmt->bind_param('s', $candidate);
            $stmt->execute();
            $stmt->bind_result($blob);
            $found = $stmt->fetch();
            $stmt->close();

            if ($found && $blob !== null) {
                return $blob;
            }
        }
    }

    // Fallback for installations where `id` is stored as BINARY(16).
    if (preg_match('/^[0-9a-f]{32}$/', $compact) === 1) {
        foreach ($tables as $tableName) {
            $stmt = $conn->prepare("SELECT data FROM {$tableName} WHERE id=UNHEX(?) LIMIT 1");
            if ($stmt !== false) {
                $stmt->bind_param('s', $compact);
                $stmt->execute();
                $stmt->bind_result($blob);
                $found = $stmt->fetch();
                $stmt->close();
                if ($found && $blob !== null) {
                    return $blob;
                }
            }
        }
    }

    return null;
}

function render_jp2_preview(?string $imgData, string $assetUuid): string
{
    if ($assetUuid === '') {
        return '<p class="hint">Kein Bild im Profil eingetragen.</p>';
    }

    if ($imgData === null || $imgData === '') {
        return '<p class="hint">Asset konnte nicht geladen werden (UUID vorhanden, aber kein Treffer in `assets`).</p>';
    }

    if (!class_exists('Imagick')) {
        return '<p class="hint">Keine Vorschau verfuegbar (Imagick fehlt), Download ist moeglich.</p>';
    }

    try {
        $imagickClass = 'Imagick';
        $im = new $imagickClass();
        $im->readImageBlob($imgData);
        $im->setImageFormat('jpeg');
        $jpegData = $im->getImageBlob();
        $im->clear();
        $im->destroy();
        return "<img src='data:image/jpeg;base64," . base64_encode($jpegData) . "' alt='Bildvorschau' class='image-preview'>";
    } catch (Exception $e) {
        return '<p class="hint">Vorschau konnte nicht erzeugt werden.</p>';
    }
}

$userInput = trim($_GET['user'] ?? '');
$isSearch = isset($_GET['user']);
$error = '';
$profile = null;
$avatarName = '';
$uuid = '';
$imgData = null;
$firstImgData = null;

$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($db->connect_errno) {
    $error = 'Datenbankverbindung fehlgeschlagen.';
}

$assetDb = null;
if ($error === '') {
    $assetDbName = defined('DB_ASSET_NAME') ? DB_ASSET_NAME : DB_NAME;
    if (!empty($assetDbName) && $assetDbName !== DB_NAME) {
        $assetDb = @new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, $assetDbName);
        if ($assetDb->connect_errno) {
            $assetDb = null;
        }
    }
}

if ($error === '' && $isSearch) {
    if ($userInput === '') {
        $error = 'Bitte gib einen Benutzernamen oder eine UUID ein.';
    } else {
        $isUuid = preg_match('/^[0-9a-fA-F-]{36}$/', $userInput) === 1;
        if ($isUuid) {
            $uuid = $userInput;
        } else {
            $parts = preg_split('/\s+/', $userInput);
            if ($parts === false || count($parts) < 2) {
                $error = 'Bitte gib Vor- und Nachnamen an (z.B. Max Mustermann).';
            } else {
                $first = $parts[0];
                $last = $parts[1];
                $stmt = $db->prepare('SELECT PrincipalID, FirstName, LastName FROM UserAccounts WHERE FirstName=? AND LastName=? LIMIT 1');
                if ($stmt === false) {
                    $error = 'Fehler beim Lesen der Benutzerdaten.';
                } else {
                    $stmt->bind_param('ss', $first, $last);
                    $stmt->execute();
                    $stmt->bind_result($uuid, $firstNameResult, $lastNameResult);
                    $found = $stmt->fetch();
                    $stmt->close();
                    if (!$found || $uuid === '') {
                        $error = 'Benutzer nicht gefunden.';
                    } else {
                        $avatarName = trim((string) $firstNameResult . ' ' . (string) $lastNameResult);
                    }
                }
            }
        }
    }
}

if ($error === '' && $uuid !== '') {
    $stmt = $db->prepare('SELECT * FROM userprofile WHERE useruuid=? LIMIT 1');
    if ($stmt === false) {
        $error = 'Fehler beim Lesen des Profils.';
    } else {
        $stmt->bind_param('s', $uuid);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result ? $result->fetch_assoc() : null;
        $stmt->close();
    }

    if ($profile === null) {
        $error = 'Kein Profil fuer diesen Benutzer gefunden.';
    }
}

if ($error === '' && $uuid !== '' && $avatarName === '') {
    $stmt = $db->prepare('SELECT FirstName, LastName FROM UserAccounts WHERE PrincipalID=? LIMIT 1');
    if ($stmt !== false) {
        $stmt->bind_param('s', $uuid);
        $stmt->execute();
        $stmt->bind_result($firstNameResult, $lastNameResult);
        if ($stmt->fetch()) {
            $avatarName = trim((string) $firstNameResult . ' ' . (string) $lastNameResult);
        }
        $stmt->close();
    }
}

if ($error === '' && $profile !== null) {
    if (!empty($profile['profileImage'])) {
        $imgData = fetch_asset_blob($db, trim((string) $profile['profileImage']));
        if ($imgData === null && $assetDb instanceof mysqli) {
            $imgData = fetch_asset_blob($assetDb, trim((string) $profile['profileImage']));
        }
    }

    if (!empty($profile['profileFirstImage'])) {
        $firstImgData = fetch_asset_blob($db, trim((string) $profile['profileFirstImage']));
        if ($firstImgData === null && $assetDb instanceof mysqli) {
            $firstImgData = fetch_asset_blob($assetDb, trim((string) $profile['profileFirstImage']));
        }
    }
}

$title = $avatarName !== '' ? $avatarName : ($uuid !== '' ? $uuid : 'OpenSim Nutzerprofil');

echo <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenSim Nutzerprofil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5efe3;
            --bg-soft: #fff8ef;
            --card: #ffffff;
            --ink: #102026;
            --muted: #54636b;
            --line: #d6dfd2;
            --accent: #0f766e;
            --accent-2: #c26a20;
            --danger: #8f1d1d;
            --danger-bg: #fce8e8;
            --info-bg: #e8f5ee;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--ink);
            background:
                radial-gradient(circle at 90% 5%, rgba(194, 106, 32, 0.2), transparent 42%),
                radial-gradient(circle at 5% 92%, rgba(15, 118, 110, 0.18), transparent 40%),
                linear-gradient(145deg, var(--bg), var(--bg-soft));
            font-family: 'Manrope', sans-serif;
            min-height: 100vh;
            line-height: 1.55;
        }

        .container {
            width: min(1160px, 94vw);
            margin: 28px auto 42px;
        }

        .hero {
            background: linear-gradient(110deg, #ffffff, #f3f8f7);
            border: 1px solid #deebe2;
            border-radius: 18px;
            padding: 26px;
            box-shadow: 0 10px 30px rgba(10, 35, 28, 0.08);
            margin-bottom: 18px;
        }

        h1, h2, h3 {
            font-family: 'Space Grotesk', sans-serif;
            margin: 0;
            line-height: 1.2;
        }

        h1 { font-size: clamp(1.55rem, 2.7vw, 2.15rem); }
        h2 { font-size: 1.2rem; margin-bottom: 12px; }
        h3 { font-size: 1rem; margin-bottom: 8px; color: #244952; }

        .sub {
            margin-top: 8px;
            color: var(--muted);
            max-width: 68ch;
        }

        .search {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            margin-top: 18px;
        }

        .input {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 0.98rem;
            background: #fff;
        }

        .button {
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 700;
            cursor: pointer;
            color: #fff;
            background: linear-gradient(135deg, var(--accent), #145a7d);
            transition: transform 0.12s ease, filter 0.12s ease;
        }

        .button:hover { transform: translateY(-1px); filter: saturate(1.08); }

        .notice {
            margin-top: 14px;
            border-radius: 12px;
            padding: 11px 14px;
            border: 1px solid transparent;
            font-size: 0.96rem;
        }

        .notice-error {
            background: var(--danger-bg);
            border-color: #f4caca;
            color: var(--danger);
        }

        .notice-info {
            background: var(--info-bg);
            border-color: #c6e8d5;
            color: #15593d;
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 2.2fr) minmax(250px, 1fr);
            gap: 18px;
            margin-top: 18px;
        }

        .card {
            background: var(--card);
            border: 1px solid #dde7df;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(11, 36, 23, 0.07);
            padding: 18px;
        }

        .meta {
            margin-top: 8px;
            color: var(--muted);
            font-size: 0.94rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .field {
            border: 1px solid #ebf0ea;
            border-radius: 12px;
            padding: 10px 11px;
            background: #fbfdfa;
        }

        .label {
            display: block;
            font-size: 0.78rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #4f6863;
            margin-bottom: 4px;
        }

        .value {
            overflow-wrap: anywhere;
        }

        .text-block {
            border: 1px solid #ebf0ea;
            border-radius: 12px;
            padding: 12px;
            margin-top: 12px;
            background: #fbfdfa;
        }

        .empty {
            color: #7c8a85;
            font-style: italic;
        }

        .hint {
            margin: 8px 0 0;
            color: #5f6d76;
            font-size: 0.92rem;
        }

        .image-preview {
            width: 100%;
            max-width: 250px;
            border-radius: 10px;
            border: 1px solid #d4ddd7;
            margin-top: 10px;
            background: #f1f5f3;
            display: block;
        }

        .download {
            margin-top: 10px;
            display: inline-block;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-2), #a95210);
            border-radius: 10px;
            padding: 8px 11px;
            font-size: 0.88rem;
        }

        .image-block + .image-block {
            margin-top: 16px;
            border-top: 1px dashed #dce6df;
            padding-top: 14px;
        }

        @media (max-width: 900px) {
            .layout { grid-template-columns: 1fr; }
            .search { grid-template-columns: 1fr; }
            .button { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="container">
        <section class="hero">
            <h1>OpenSim Nutzerprofil</h1>
            <p class="sub">Suche nach einem Avatar via <strong>Vorname Nachname</strong> oder direkt per <strong>UUID</strong>. Die Ansicht zeigt Profildaten, Freitexte und vorhandene Bild-Assets in einer besser lesbaren Struktur.</p>
            <form class="search" method="get" action="">
HTML;

echo '<input class="input" type="text" name="user" id="user" value="' . h($userInput) . '" required placeholder="z.B. Max Mustermann oder 00000000-0000-0000-0000-000000000000">';

echo <<<HTML
                <button class="button" type="submit">Profil anzeigen</button>
            </form>
HTML;

if ($error !== '') {
    echo '<div class="notice notice-error">' . h($error) . '</div>';
} elseif (!$isSearch) {
    echo '<div class="notice notice-info">Noch keine Suche ausgefuehrt. Gib oben einen Namen oder eine UUID ein.</div>';
}

echo '</section>';

if ($error === '' && $profile !== null) {
    $displayName = $avatarName !== '' ? $avatarName : 'Unbekannter Avatar';
    $profileImageUuid = trim((string) ($profile['profileImage'] ?? ''));
    $firstImageUuid = trim((string) ($profile['profileFirstImage'] ?? ''));
    $profileImageFile = $profileImageUuid !== '' && $imgData !== null ? 'data:image/jp2;base64,' . base64_encode($imgData) : '';
    $firstImageFile = $firstImageUuid !== '' && $firstImgData !== null ? 'data:image/jp2;base64,' . base64_encode($firstImgData) : '';

    echo '<section class="layout">';
    echo '<article class="card">';
    echo '<h2>' . h($displayName) . '</h2>';
    echo '<p class="meta">UUID: <code>' . h($uuid) . '</code></p>';

    echo '<h3 style="margin-top: 16px;">Basisdaten</h3>';
    echo '<div class="grid">';
    echo '<div class="field"><span class="label">Partner UUID</span><div class="value"><code>' . format_single($profile['profilePartner'] ?? '') . '</code></div></div>';
    echo '<div class="field"><span class="label">Mature Publish</span><div class="value">' . bool_to_label($profile['profileMaturePublish'] ?? '') . '</div></div>';
    echo '<div class="field"><span class="label">Profil URL</span><div class="value">' . format_single($profile['profileURL'] ?? '') . '</div></div>';
    echo '<div class="field"><span class="label">Sprachen</span><div class="value">' . format_multiline($profile['profileLanguages'] ?? '') . '</div></div>';
    echo '</div>';

    echo '<h3 style="margin-top: 16px;">Interessen und Skills</h3>';
    echo '<div class="grid">';
    echo '<div class="field"><span class="label">Will (Text)</span><div class="value">' . format_multiline($profile['profileWantToText'] ?? '') . '</div></div>';
    echo '<div class="field"><span class="label">Skills (Text)</span><div class="value">' . format_multiline($profile['profileSkillsText'] ?? '') . '</div></div>';
    echo '</div>';

    echo '<h3 style="margin-top: 16px;">Freitexte</h3>';
    echo '<div class="text-block"><span class="label">Ueber mich</span><div class="value">' . format_multiline($profile['profileAboutText'] ?? '') . '</div></div>';
    echo '<div class="text-block"><span class="label">Erster Text</span><div class="value">' . format_multiline($profile['profileFirstText'] ?? '') . '</div></div>';

    echo '</article>';

    echo '<aside class="card">';
    echo '<h2>Bilder</h2>';

    echo '<div class="image-block">';
    echo '<h3>Profilbild</h3>';
    echo '<p class="meta">Asset UUID: <code>' . format_single($profileImageUuid) . '</code></p>';
    if ($profileImageFile !== '') {
        echo '<a class="download" href="' . $profileImageFile . '" download="profile.jp2">Download JP2</a>';
    }
    echo render_jp2_preview($imgData, $profileImageUuid);
    echo '</div>';

    echo '<div class="image-block">';
    echo '<h3>Erstes Bild</h3>';
    echo '<p class="meta">Asset UUID: <code>' . format_single($firstImageUuid) . '</code></p>';
    if ($firstImageFile !== '') {
        echo '<a class="download" href="' . $firstImageFile . '" download="firstimage.jp2">Download JP2</a>';
    }
    echo render_jp2_preview($firstImgData, $firstImageUuid);
    echo '</div>';

    echo '</aside>';
    echo '</section>';
}

echo '</main></body></html>';

if ($assetDb instanceof mysqli) {
    $assetDb->close();
}
if ($db instanceof mysqli) {
    $db->close();
}
?>
