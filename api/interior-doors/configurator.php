<?php

require_once __DIR__ . "/../../php/api/common/facade/api-common-facade.php";
require_once __DIR__ . "/../../php/api/common/facade/json-data-manipulation-facade.php";
require_once __DIR__ . "/../../php/api/interior-doors/facade/campaign-facade.php";
require_once __DIR__ . "/../../php/api/interior-doors/facade/configurator-facade.php";
require_once __DIR__ . "/../../php/api/interior-doors/req/api-configurator-request-objects.php";
require_once __DIR__ . "/../../php/api/interior-doors/res/api-configurator-response-objects.php";
require_once __DIR__ . "/../../php/api/interior-doors/model/cart-model.php";
require_once __DIR__ . "/../../php/enums/CurrencyEnum.php";
require_once __DIR__ . "/../../php/enums/SupportedLanguagesEnum.php";

startApiSession();
//session_destroy();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $currencyId = CurrencyEnum::isValid($_GET['currency']) ? $_GET['currency'] : CurrencyEnum::EUR;
    $doorCategories = mapDoorCategoriesToResponse($currencyId);
    $gallery = GalleryJsonDataManipulation::getAll();
    $glasses = GlassesJsonDataManipulation::getAll();
    $handles = mapHandlesToResponseInteriorDoors($currencyId);
    $materials = MaterialsJsonDataManipulation::getAll();
    $rooms = RoomsJsonDataManipulation::getAll();

    sendJsonResponse(
        new ConfiguratorResponse(
            currencyRateTransformation($currencyId, ParametersJsonDataManipulation::getAll()["assemblyDoorsCost"] ?? 0.0, null),
            $doorCategories,
            currencyRateTransformation($currencyId, ParametersJsonDataManipulation::getAll()["framePrice"] ?? 0.0, null),
            $gallery,
            $glasses,
            $handles,
            currencyRateTransformation($currencyId, ParametersJsonDataManipulation::getAll()["interiorDoorsPriceFrom"] ?? 0.0, null),
            $materials,
            $rooms
        ),
        200
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents("php://input");
    $requestBody = json_decode($rawData, true);
    $lang = SupportedLanguagesEnum::isValid($_GET['lang']) ? $_GET['lang'] : SupportedLanguagesEnum::SK;

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(['error' => 'Invalid JSON'], 400);
    }

    try {
        $response = processInteriorDoorsConfiguratorPost($requestBody, $lang);
        sendJsonResponse($response, 200);
    } catch (Throwable $exception) {
        sendJsonResponse(['error' => 'Configurator request failed'], 500);
    }
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