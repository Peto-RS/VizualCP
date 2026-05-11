<?php
require_once __DIR__ . "/api-common.php";
require_once __DIR__ . "/php/api/model/cart-model.php";
require_once __DIR__ . "/php/api/price-offer-facade.php";
require_once __DIR__ . "/php/api/request-objects/api-price-offer-request-objects.php";
require_once __DIR__ . "/php/api/response-objects/api-price-offer-response-objects.php";
require_once __DIR__ . "/php/api/request-objects/api-configurator-request-objects.php";
require_once __DIR__ . "/php/api/model/validation-message.php";

session_start();
//session_destroy();

// ---------------------
// Handle GET requests
// ---------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['getApiResponse'])) {
        try {
            $apiResponse = new ApiResponse(
                DistrictsJsonDataManipulation::getAll() ?? array(),
                getPriceOfferResponse()
            );
            sendJsonResponse($apiResponse, 200);
        } catch (Throwable $e) {
            error_log($e);
            sendJsonResponse(['error' => $e->getTrace()], 500);
        }
    } else {
        sendJsonResponse(['error' => 'Missing getApiResponse parameter'], 400);
    }
}

// ---------------------
// Handle POST requests (JSON body)
// ---------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents("php://input");
    $requestBody = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(['error' => 'Invalid JSON'], 400);
    }

    sendJsonResponse(createOrUpdatePriceOffer($requestBody), 200);
}

// ---------------------
// If neither GET nor POST
// ---------------------
sendJsonResponse(['error' => 'Unsupported request method'], 405);