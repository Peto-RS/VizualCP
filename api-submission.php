<?php
require_once __DIR__ . "/api-common.php";
require_once __DIR__ . "/php/api/model/cart-model.php";
require_once __DIR__ . "/php/api/json-data-manipulation-facade.php";
require_once __DIR__ . "/php/api/submission-facade.php";
require_once __DIR__ . "/php/api/price-offer-spreadsheet-facade.php";
require_once __DIR__ . "/php/api/price-offer-mail-facade.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawData = file_get_contents("php://input");
    $requestBody = json_decode($rawData, true);
    $validations = submitPriceOffer($requestBody);

    if (count($validations) > 0) {
        sendJsonResponse($validations, 400);
    } else {
        sendJsonResponse(array(), 200);
    }
}

sendJsonResponse(["error" => "Unsupported request method."], 405);