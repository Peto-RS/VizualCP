<?php

require_once __DIR__ . "/../../php/api/common/facade/api-common-facade.php";
require_once __DIR__ . "/../../php/api/common/facade/json-data-manipulation-facade.php";
require_once __DIR__ . "/../../php/api/security-doors/facade/configurator-facade.php";
require_once __DIR__ . "/../../php/api/security-doors/model/model-security-doors.php";
require_once __DIR__ . "/../../php/api/security-doors/req/api-configurator-request-objects.php";
require_once __DIR__ . "/../../php/api/security-doors/res/api-configurator-response-objects.php";
require_once __DIR__ . "/../../php/enums/CurrencyEnum.php";
require_once __DIR__ . "/../../php/enums/SupportedLanguagesEnum.php";

startApiSession();
//session_destroy();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $currencyId = CurrencyEnum::isValid($_GET['currency']) ? $_GET['currency'] : CurrencyEnum::EUR;
    $equipmentIncluded = SecurityDoorEquipmentIncludedJsonDataManipulation::getAll();
    $equipmentOptional = mapOptionalEquipmentToResponse($currencyId);
    $materials = SecurityDoorMaterialsJsonDataManipulation::getAll();
    $frameColors = SecurityDoorFrameColorsJsonDataManipulation::getAll();
    $doorCategories = mapSecurityDoorCategoriesToResponse($currencyId);
    $handles = mapHandlesToResponseInteriorDoors($currencyId);
    $rooms = RoomsJsonDataManipulation::getAll();

    sendJsonResponse(
        new SecurityDoorsConfiguratorResponse(
            $doorCategories,
            $equipmentIncluded,
            $equipmentOptional,
            $frameColors,
            $handles,
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
        $response = processSecurityDoorsConfiguratorPost($requestBody, $lang);
        sendJsonResponse($response, 200);
    } catch (Throwable $exception) {
        sendJsonResponse(['error' => 'Configurator request failed'], 500);
    }
}

sendJsonResponse(['error' => 'Unsupported request method'], 405);