<?php
require_once __DIR__ . "/../../common/facade/db-facade.php";
require_once __DIR__ . "/../../common/facade/api-common-facade.php";
require_once __DIR__ . "/../res/api-price-offer-response-objects.php";
require_once __DIR__ . "/campaign-facade.php";
require_once __DIR__ . "/../model/cart-model.php";
require_once __DIR__ . "/../../../enums/ErrorCodeEnum.php";

function getPriceOfferResponse(
    string  $currency,
    ?string $lang
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
        getActiveCampaignsOfType(CampaignTypeEnum::DOORS, new DateTime(), AppConfigJsonDataManipulation::getAll()["campaignDisabled"] ? array() : CampaignsJsonDataManipulation::getAll())
    );
}


/**
 * @return ValidationMessage[]
 */
function createOrUpdatePriceOffer(array $requestObj, ?string $lang): array
{
    try {
        $parsedObject = new PriceOfferApiRequest($requestObj);
        $priceOffer = PriceOffer::fromRequest($parsedObject->priceOffer, $lang);
        $_SESSION['priceOffer'] = $priceOffer;

        return validate($parsedObject);
    } catch (Throwable $exception) {
        $route = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: null;
        error_log($exception);
        createErrorLogRecord(
            getDbConnection(),
            $_SESSION['session_ref'] ?? null,
            ErrorCodeEnum::INTERIOR_DOORS_PRICE_OFFER_POST_FAILED,
            $exception->getMessage(),
            'price_offer',
            $exception->__toString(),
            $route
        );

        throw $exception;
    }
}