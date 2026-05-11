<?php
require_once __DIR__ . "/api-common.php";
require_once __DIR__ . "/php/api/response-objects/api-app-config-response-objects.php";
require_once __DIR__ . "/php/api/model/cart-model.php";
require_once __DIR__ . "/php/api/model/validation-message.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $appConfig = AppConfigJsonDataManipulation::getAll();

    sendJsonResponse(
        new AppConfigResponse(
            $appConfig["baseUrl"],
            boolval($appConfig["reCaptchaEnabled"]),
            $appConfig["reCaptchaSiteKey"]
        ),
        200
    );
}

sendJsonResponse(['error' => 'Unsupported request method'], 405);