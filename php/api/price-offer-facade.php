<?php
require_once __DIR__ . "/response-objects/api-price-offer-response-objects.php";
require_once __DIR__ . "/campaign-facade.php";

function getPriceOfferResponse(
    string  $currency,
    ?string $lang,
    bool    $campaignDisabled
): PriceOfferResponse
{
    if (!isPriceOfferInSessionValid($_SESSION)) {
        error_log("Session price offer is invalid, resetting." . json_encode($_SESSION));
        $_SESSION['priceOffer'] = new PriceOffer($lang);
    }

    /** @var PriceOffer $sessionPriceOffer */
    $sessionPriceOffer = $_SESSION['priceOffer'];
    return PriceOffer::fromSession($sessionPriceOffer)->toResponse(
        $currency,
        $campaignDisabled ? array() : getActiveCampaignsOfType(CampaignTypeEnum::DOORS, new DateTime())
    );
}


/**
 * @return ValidationMessage[]
 */
function createOrUpdatePriceOffer(array $requestObj, ?string $lang): array
{
    $parsedObject = new PriceOfferApiRequest($requestObj);
    $priceOffer = PriceOffer::fromRequest($parsedObject->priceOffer, $lang);
    $_SESSION['priceOffer'] = $priceOffer;

    return validate($parsedObject);
}