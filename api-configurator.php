<?php
include_once "api-common.php";
include_once "json-data-manipulation.php";
include_once("php/api/request-objects/api-configurator-request-objects.php");
include_once("php/api/response-objects/api-configurator-response-objects.php");

session_start();
//session_destroy();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $doorCategories = DoorCategoriesJsonDataManipulation::getAll();
    $gallery = GalleryJsonDataManipulation::getAll();
    $glasses = GlassesJsonDataManipulation::getAll();
    $handles = HandlesJsonDataManipulation::getAll();
    $materials = MaterialsJsonDataManipulation::getAll();
    $rooms = RoomsJsonDataManipulation::getAll();

    sendJsonResponse(
        new ConfiguratorResponse($doorCategories, $gallery, $glasses, $handles, $materials, $rooms),
        200
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents("php://input");
    $requestBody = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(['error' => 'Invalid JSON'], 400);
    }

    $parsedObject = new ConfiguratorPostRequest($requestBody);
    $priceOffer = isset($_SESSION['priceOffer']) ? PriceOffer::fromSession($_SESSION['priceOffer']) : new PriceOffer();
    $priceOffer->fromPostConfigurator($parsedObject);
    $_SESSION['priceOffer'] = $priceOffer;

    sendJsonResponse($priceOffer->toResponse(), 200);
}

sendJsonResponse(['error' => 'Unsupported request method'], 405);

/*
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('#/add-door$#', $path)) {
    $parsedObject = new ConfiguratorPostRequest($requestBody);
    $priceOffer = $_SESSION['priceOffer'] ? PriceOffer::fromSession($_SESSION['priceOffer']) : new PriceOffer();
    $priceOffer->postConfigurator($parsedObject);
    $_SESSION['priceOffer'] = $priceOffer;

    sendJsonResponse($priceOffer, 200);
}
*/
?>