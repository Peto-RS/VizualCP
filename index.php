<?php
require_once __DIR__ . "/php/api/model/cart-model.php";
require_once __DIR__ . "/php/api/json-data-manipulation-facade.php";

$nonce = bin2hex(random_bytes(16));

session_start();
//session_destroy();
//Require https
/*
if ($_SERVER['HTTPS'] != "on"){
    $url = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    header("Location: $url");
}*/
?>

<html lang="sk">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Visualizer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
          content="Prezrite si našu VIZUALIZÁCIU interiérových dverí, kde nájdete viac ako 100 druhov dverí v 40 rôznych farebných dekoroch, usporiadaných v 8 prehľadných kategóriách."/>
    <meta http-equiv="Content-Security-Policy"
          content="
      default-src 'self';

      script-src
        'self'
        'nonce-<?php echo $nonce; ?>'
        https://cdn.jsdelivr.net
        https://www.google.com
        https://www.gstatic.com
        https://www.googletagmanager.com
        https://www.google-analytics.com
        http://localhost:5173;

      style-src
        'self'
        'unsafe-inline'
        https://use.fontawesome.com
        http://localhost:5173;

      font-src
        'self'
        data:
        https://fonts.gstatic.com
        https://use.fontawesome.com;

      img-src
        'self'
        data:
        blob:
        https://www.google-analytics.com;

      connect-src
        'self'
        https://www.google-analytics.com
        https://region1.google-analytics.com
        https://www.googletagmanager.com
        ws://localhost:5173
        http://localhost:5173;

      frame-src
        https://www.google.com
        https://www.youtube.com
        https://www.youtube-nocookie.com;

      object-src 'none';
      base-uri 'self';
    ">
    <link rel="stylesheet"
          href="<?php echo(AppConfigJsonDataManipulation::getAll()["isProductionVueBuild"] ? AppConfigJsonDataManipulation::getAll()["baseUrl"] . "/public/assets/main.css" : "http://localhost:5173/src/main.css") ?>">

    <?php
    require_once __DIR__ . "/php/api/json-data-manipulation-facade.php";
    ?>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
</head>

<body>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php
require_once __DIR__ . "/php/api/json-data-manipulation-facade.php";
$config = AppConfigJsonDataManipulation::getAll();

if (!empty($config['ga4']) && is_array($config['ga4'])) {
    $firstGa4 = $config['ga4'][0];
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($firstGa4); ?>"></script>
    <script nonce="<?php echo $nonce; ?>">
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        <?php
        foreach ($config['ga4'] as $ga4) {
            echo "gtag('config', '" . addslashes($ga4) . "');\n";
        }
        ?>
    </script>
    <?php
}
?>

<?php
require_once __DIR__ . "/php/api/json-data-manipulation-facade.php";
?>
<script type="module"
        src="<?php echo(AppConfigJsonDataManipulation::getAll()["isProductionVueBuild"] ? AppConfigJsonDataManipulation::getAll()["baseUrl"] . "/public/app.js" : "http://localhost:5173/src/main.ts") ?>"></script>
<div id="vueAppFull"></div>
</body>
</html>