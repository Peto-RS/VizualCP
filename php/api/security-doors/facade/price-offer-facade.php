<?php
require_once __DIR__ . "/../../common/facade/db-facade.php";
require_once __DIR__ . "/../../common/facade/api-common-facade.php";
require_once __DIR__ . "/price-offer-validation-facade.php";
require_once __DIR__ . "/../res/api-price-offer-response-objects.php";
require_once __DIR__ . "/../../../enums/ErrorCodeEnum.php";

function getSecurityDoorsPriceOfferResponse(
    string  $currency,
    ?string $lang
): SecurityDoorsPriceOfferResponse
{
    if (!isSecurityDoorsPriceOfferInSessionValid($_SESSION)) {
        error_log("Session price offer is invalid, resetting." . json_encode($_SESSION));
        $_SESSION['securityDoorsPriceOffer'] = new SecurityDoorsPriceOffer($lang);
    }

    /** @var SecurityDoorsPriceOffer $sessionPriceOffer */
    $sessionPriceOffer = $_SESSION['securityDoorsPriceOffer'];
    return SecurityDoorsPriceOffer::fromSession($sessionPriceOffer)->toResponse(
        $currency
    );
}


/**
 * @return ValidationMessage[]
 */
function createOrUpdateSecurityDoorsPriceOffer(array $requestObj, ?string $lang): array
{
    try {
        $parsedObject = new SecurityDoorsPriceOfferApiRequest($requestObj);
        $priceOffer = SecurityDoorsPriceOffer::fromRequest($parsedObject->priceOffer, $lang);
        $_SESSION['securityDoorsPriceOffer'] = $priceOffer;

        return validateSecurityDoorsPriceOfferForm($parsedObject);
    } catch (Throwable $exception) {
        $route = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: null;
        error_log($exception);
        createErrorLogRecord(
            getDbConnection(),
            $_SESSION['session_ref'] ?? null,
            ErrorCodeEnum::SECURITY_DOORS_PRICE_OFFER_POST_FAILED,
            $exception->getMessage(),
            'price_offer',
            $exception->__toString(),
            $route
        );

        throw $exception;
    }
}