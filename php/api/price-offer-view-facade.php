<?php

require_once __DIR__ . "/response-objects/api-price-offer-response-objects.php";
require_once __DIR__ . "/json-data-manipulation-facade.php";

function getPriceOfferViewResponse(string $currency, string $lang, bool $campaignDisabled): PriceOfferViewResponse
{
    $priceOfferResponse = getPriceOfferResponse($currency, $lang, $campaignDisabled);
    $country = $priceOfferResponse->address->deliveryCountry;
    $districts = DistrictsJsonDataManipulation::getAllByCountry($country) ?? array();

    return new PriceOfferViewResponse(
        DeliveryCountriesJsonDataManipulation::getAll(),
        $districts,
        $priceOfferResponse
    );
}