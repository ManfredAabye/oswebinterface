<?php
require_once 'config.php';
// Tailwind CSS Framework
?>

<!DOCTYPE html>
<html lang="de">
<link rel="icon" type="image/x-icon" href="include/favicon.ico">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: <?php echo FONT_FAMILY; ?>;
            background-color: var(--secondary-color);
            color: var(--primary-color);
            background-image: url(<?php echo BACKGROUND_IMAGE; ?>);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            height: 100vh;
            width: 100vw;
            opacity: <?php echo defined('BACKGROUND_OPACITY') ? BACKGROUND_OPACITY : 1; ?>;
        }
        header {
            background-color: var(--header-color);
            padding: 10px;
            text-align: center;
            color: white;
        }
        footer {
            background-color: var(--footer-color);
            margin: 0;
            padding: 0;
            text-align: center;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
            line-height: 0;
        }
        nav a {
            margin: 0 10px;
            color: white;
            text-decoration: none;
        }
        .container {
            background-image: url(<?php echo FOREGROUND_IMAGE; ?>);
            background-size: 50%;
            background-repeat: no-repeat;
            background-position: center;
            padding: 20px;
            border-radius: 8px;
            opacity: <?php echo defined('FOREGROUND_OPACITY') ? FOREGROUND_OPACITY : 1; ?>;
        }
        .color-button {
            margin: 1px;
            padding: 1px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 10px;
            color: white;
        }
    </style>
    <script>
        const colorSchemes = <?php echo json_encode($colorSchemes); ?>;

        function setColorScheme(scheme) {
            document.documentElement.style.setProperty('--header-color', colorSchemes[scheme].header);
            document.documentElement.style.setProperty('--footer-color', colorSchemes[scheme].footer);
            document.documentElement.style.setProperty('--secondary-color', colorSchemes[scheme].secondary);
            document.documentElement.style.setProperty('--primary-color', colorSchemes[scheme].primary);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.color-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    setColorScheme(this.dataset.scheme);
                });
            });
            // Set the initial color scheme
            setColorScheme('<?php echo INITIAL_COLOR_SCHEME; ?>');
        });
    </script>
</head>
<body class="bg-secondary-color text-primary-color">
<header class="bg-header-color p-4 text-center text-white">
    <h1>Welcome to <?php echo SITE_NAME; ?></h1>
</header>
<nav class="bg-header-color p-4 text-center">
    <div>
    <?php if (SHOW_COLOR_BUTTONS): ?>
        <?php foreach ($colorSchemes as $scheme => $colors): ?>
            <button class="color-button bg-primary-color text-white m-2 p-2 rounded" data-scheme="<?php echo $scheme; ?>" style="background-color: <?php echo $colors['header']; ?>;"><?php echo ucfirst($scheme); ?></button>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</nav>

<footer class="bg-footer-color p-4 text-center text-white">
    <p>&copy; <?php echo date("Y"); ?> <?php echo SITE_NAME; ?>. Alle Rechte vorbehalten.</p>    
</footer>
</body>
</html>
