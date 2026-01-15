<?php
include_once "cart-model.php";

session_start();

function sendJsonResponse($data, int $statusCode)
{
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function isPriceOfferInSessionValid(): bool
{
    return !(isset($_SESSION['priceOffer']) && $_SESSION['priceOffer'] instanceof __PHP_Incomplete_Class);
}

function getPriceOfferFromSessionOrUnset(): ?PriceOffer
{
    if (!isPriceOfferInSessionValid()) {
        unset($_SESSION['priceOffer']);
        error_log('Removed incomplete session object priceOffer');
        return null;
    } else {
        return $_SESSION['priceOffer'];
    }
}