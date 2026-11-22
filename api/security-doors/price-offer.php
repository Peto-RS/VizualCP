<?php
require_once __DIR__ . "/../../php/api/common/facade/api-common-facade.php";
require_once __DIR__ . "/../../php/api/security-doors/facade/price-offer-facade.php";
require_once __DIR__ . "/../../php/api/security-doors/facade/price-offer-view-facade.php";
require_once __DIR__ . "/../../php/api/security-doors/model/model-security-doors.php";
require_once __DIR__ . "/../../php/api/security-doors/req/api-price-offer-request-objects.php";
require_once __DIR__ . "/../../php/api/security-doors/res/api-price-offer-response-objects.php";
require_once __DIR__ . "/../../php/api/security-doors/req/api-configurator-request-objects.php";
require_once __DIR__ . "/../../php/api/common/validation-message.php";
require_once __DIR__ . "/../../php/enums/SupportedLanguagesEnum.php";

startApiSession();
//session_destroy();

// ---------------------
// Handle GET requests
// ---------------------
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $currencyId = CurrencyEnum::isValid($_GET['currency']) ? $_GET['currency'] : CurrencyEnum::EUR;
        $lang = SupportedLanguagesEnum::isValid($_GET['lang']) ? $_GET['lang'] : SupportedLanguagesEnum::SK;
        $response = getSecurityDoorsPriceOfferViewResponse($currencyId, $lang);
        sendJsonResponse($response, 200);
    } catch (Throwable $e) {
        error_log($e);
        sendJsonResponse(['error' => $e->getTrace()], 500);
    }
}

// ---------------------
// Handle POST requests (JSON body)
// ---------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rawData = file_get_contents("php://input");
    $requestBody = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(['error' => 'Invalid JSON'], 400);
    }

    $lang = SupportedLanguagesEnum::isValid($_GET['lang']) ? $_GET['lang'] : SupportedLanguagesEnum::SK;
    try {
        $response = createOrUpdateSecurityDoorsPriceOffer($requestBody, $lang);
        sendJsonResponse($response, 200);
    } catch (Throwable $exception) {
        sendJsonResponse(['error' => 'Price offer request failed'], 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_SESSION['securityDoorsPriceOffer'])) {
        unset($_SESSION['securityDoorsPriceOffer']);
    }

    http_response_code(204);
    exit;
}

// ---------------------
// If neither GET nor POST
// ---------------------
sendJsonResponse(['error' => 'Unsupported request method'], 405);