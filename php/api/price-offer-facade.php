<?php
require_once __DIR__ . "/response-objects/api-price-offer-response-objects.php";

function getPriceOfferResponse(): PriceOfferResponse
{
    if (!isPriceOfferInSessionValid($_SESSION)) {
        error_log("Session price offer is invalid, resetting." . json_encode($_SESSION));
        $_SESSION['priceOffer'] = new PriceOffer();
    }

    /** @var PriceOffer $sessionPriceOffer */
    $sessionPriceOffer = $_SESSION['priceOffer'];
    return PriceOffer::fromSession($sessionPriceOffer)->toResponse();
}


/**
 * @return ValidationMessage[]
 */
function createOrUpdatePriceOffer(array $requestObj): array
{
    $parsedObject = new PriceOfferApiRequest($requestObj);
    $priceOffer = PriceOffer::fromRequest($parsedObject->priceOffer);
    $_SESSION['priceOffer'] = $priceOffer;

    return validate($parsedObject);
}