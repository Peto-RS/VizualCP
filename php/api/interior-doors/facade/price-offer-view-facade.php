<?php

require_once __DIR__ . "/../res/api-price-offer-response-objects.php";
require_once __DIR__ . "/../../common/facade/json-data-manipulation-facade.php";

function getPriceOfferViewResponse(string $currency, string $lang): PriceOfferViewResponse
{
    $priceOfferResponse = getPriceOfferResponse($currency, $lang);
    $country = $priceOfferResponse->address->deliveryCountry;
    $districts = DistrictsJsonDataManipulation::getAllByCountry($country) ?? array();

    return new PriceOfferViewResponse(
        DeliveryCountriesJsonDataManipulation::getAll(),
        $districts,
        $priceOfferResponse
    );
}