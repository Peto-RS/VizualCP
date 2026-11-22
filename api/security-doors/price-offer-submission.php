<?php
require_once __DIR__ . "/../../php/api/common/facade/api-common-facade.php";
require_once __DIR__ . "/../../php/api/common/facade/json-data-manipulation-facade.php";
require_once __DIR__ . "/../../php/api/security-doors/facade/submission-facade.php";
require_once __DIR__ . "/../../php/api/security-doors/facade/price-offer-spreadsheet-facade.php";
require_once __DIR__ . "/../../php/api/security-doors/facade/price-offer-mail-facade.php";
startApiSession();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawData = file_get_contents("php://input");
    $requestBody = json_decode($rawData, true);
    $lang = $_GET['lang'];
    $validations = submitPriceOfferSecurityDoors($requestBody, $lang);

    if (count($validations) > 0) {
        sendJsonResponse($validations, 400);
    } else {
        sendJsonResponse(array(), 200);
    }
}

sendJsonResponse(["error" => "Unsupported request method."], 405);