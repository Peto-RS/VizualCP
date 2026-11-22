<?php
require_once __DIR__ . "/../php/api/common/facade/api-common-facade.php";
require_once __DIR__ . "/../php/api/interior-doors/res/api-app-config-response-objects.php";
require_once __DIR__ . "/../php/api/common/validation-message.php";
require_once __DIR__ . "/../php/api/common/facade/json-data-manipulation-facade.php";
startApiSession();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $appConfig = AppConfigJsonDataManipulation::getAll();

    sendJsonResponse(
        new AppConfigResponse(
            $appConfig["baseUrl"],
            $appConfig["profile"],
            boolval($appConfig["reCaptchaEnabled"]),
            $appConfig["reCaptchaSiteKey"]
        ),
        200
    );
}

sendJsonResponse(['error' => 'Unsupported request method'], 405);