<?php

require_once __DIR__ . "/../res/api-price-offer-response-objects.php";
require_once __DIR__ . "/../../common/facade/json-data-manipulation-facade.php";

function getSecurityDoorsPriceOfferViewResponse(string $currency, string $lang): SecurityDoorsPriceOfferViewResponse
{
    $priceOfferResponse = getSecurityDoorsPriceOfferResponse($currency, $lang);
    $country = $priceOfferResponse->address->deliveryCountry;
    $districts = DistrictsJsonDataManipulation::getAllByCountry($country) ?? array();

    return new SecurityDoorsPriceOfferViewResponse(
        DeliveryCountriesJsonDataManipulation::getAll(),
        $districts,
        $priceOfferResponse
    );
}