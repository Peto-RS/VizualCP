<?php
include_once "cart-model.php";
include_once "json-data-manipulation.php";

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
    <link rel="stylesheet"
          href="<?php echo(AppConfigJsonDataManipulation::getAll()["isProductionVueBuild"] ? AppConfigJsonDataManipulation::getAll()["baseUrl"] . "/public/assets/main.css" : "http://localhost:5173/src/main.css") ?>">

    <?php
    include_once "json-data-manipulation.php";
    ?>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
</head>

<body>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php
include_once "json-data-manipulation.php";
$config = AppConfigJsonDataManipulation::getAll();

if (!empty($config['ga4']) && is_array($config['ga4'])) {
    $firstGa4 = $config['ga4'][0];
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($firstGa4); ?>"></script>
    <script>
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
include_once "json-data-manipulation.php";
?>
<script type="module"
        src="<?php echo(AppConfigJsonDataManipulation::getAll()["isProductionVueBuild"] ? AppConfigJsonDataManipulation::getAll()["baseUrl"] . "/public/app.js" : "http://localhost:5173/src/main.ts") ?>"></script>
<div id="vueAppFull"></div>
</body>
</html>