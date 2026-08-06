<?php
require_once __DIR__ . "/api-common.php";
require_once __DIR__ . "/php/api/model/cart-model.php";
require_once __DIR__ . "/php/api/price-offer-facade.php";
require_once __DIR__ . "/php/api/price-offer-view-facade.php";
require_once __DIR__ . "/php/api/request-objects/api-price-offer-request-objects.php";
require_once __DIR__ . "/php/api/response-objects/api-price-offer-response-objects.php";
require_once __DIR__ . "/php/api/request-objects/api-configurator-request-objects.php";
require_once __DIR__ . "/php/api/model/validation-message.php";
require_once __DIR__ . "/php/enums/SupportedLanguagesEnum.php";

session_start();
//session_destroy();

// ---------------------
// Handle GET requests
// ---------------------
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $currencyId = CurrencyEnum::isValid($_GET['currency']) ? $_GET['currency'] : CurrencyEnum::EUR;
        $lang = SupportedLanguagesEnum::isValid($_GET['lang']) ? $_GET['lang'] : SupportedLanguagesEnum::SK;
        $campaignDisabled = $_GET['campaignDisabled'] ?? false;

        $response = getPriceOfferViewResponse($currencyId, $lang, $campaignDisabled);
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
    sendJsonResponse(createOrUpdatePriceOffer($requestBody, $lang), 200);
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_SESSION['priceOffer'])) {
        unset($_SESSION['priceOffer']);
    }

    http_response_code(204);
    exit;
}

// ---------------------
// If neither GET nor POST
// ---------------------
sendJsonResponse(['error' => 'Unsupported request method'], 405);