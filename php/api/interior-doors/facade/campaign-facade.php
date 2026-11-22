<?php

require_once __DIR__ . '/../../common/facade/json-data-manipulation-facade.php';
require_once __DIR__ . '/../res/api-price-offer-response-objects.php';

function getActiveCampaignsOfType(string $type, DateTime $dateTime, array $campaigns): array
{
    $filter = array_filter($campaigns, function ($it) use ($dateTime, $type) {
        try {
            $startDateTime = new DateTime($it["startDateTime"]);
            $endDateTime = new DateTime($it["endDateTime"]);
        } catch (Exception $e) {
            return false;
        }

        return $it["type"] == $type && $dateTime >= $startDateTime && $dateTime <= $endDateTime;
    });

    return array_map(function ($it) {
        return new Campaign(
            $it["id"],
            $it["percentageDiscount"] ?? 0.0,
            $it["translationKey"],
            $it["type"]
        );
    }, $filter);
}


/**
 * @param float $calculatedDoorsPriceNoCampaign
 * @param Campaign[] $doorsCampaigns
 * @return CampaignDetailResponse[]
 */
function getCalculatedCampaignDetails(float $calculatedDoorsPriceNoCampaign, array $doorsCampaigns): array
{
    $result = array();
    $resultPrice = $calculatedDoorsPriceNoCampaign;
    foreach ($doorsCampaigns as $campaign) {
        $discountAmount = $resultPrice * ($campaign->percentageDiscount / 100.0);
        $resultPrice = $resultPrice - $discountAmount;
        $result[] = new CampaignDetailResponse(
            $discountAmount,
            $discountAmount * ParametersJsonDataManipulation::getAll()["vat"],
            $campaign->id,
            $campaign->translationKey,
            $campaign->type
        );
    }

    return $result;
}

/**
 * @param float $price
 * @param Campaign[] $campaigns
 * @return float
 */
function getCampaignPrice(float $price, array $campaigns): float
{
    $result = $price;
    foreach ($campaigns as $campaign) {
        $result = $result * (1 - ($campaign->percentageDiscount / 100.0));
    }

    return $result;
}