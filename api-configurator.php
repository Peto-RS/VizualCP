<?php
include_once "api-common.php";
include_once "json-data-manipulation.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rooms = RoomsJsonDataManipulation::getAll();

    sendJsonResponse(
        $rooms,
        200
    );
}

sendJsonResponse(['error' => 'Unsupported request method'], 405);
?>