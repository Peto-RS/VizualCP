<?php
include_once "api-common.php";
include_once "json-data-manipulation.php";
include_once("php/api/response-objects/api-configurator-response-objects.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $doorCategories = DoorCategoriesJsonDataManipulation::getAll();
    $glasses = GlassesJsonDataManipulation::getAll();
    $handles = HandlesJsonDataManipulation::getAll();
    $materials = MaterialsJsonDataManipulation::getAll();
    $rooms = RoomsJsonDataManipulation::getAll();

    sendJsonResponse(
        new ConfiguratorResponse($doorCategories, $glasses, $handles, $materials, $rooms),
        200
    );
}

sendJsonResponse(['error' => 'Unsupported request method'], 405);
?>