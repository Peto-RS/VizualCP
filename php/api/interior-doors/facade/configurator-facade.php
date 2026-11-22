<?php
require_once __DIR__ . '/../../common/facade/json-data-manipulation-facade.php';
require_once __DIR__ . '/../../common/facade/db-facade.php';
require_once __DIR__ . '/../req/api-configurator-request-objects.php';
require_once __DIR__ . '/../res/api-configurator-response-objects.php';
require_once __DIR__ . '/../res/api-price-offer-response-objects.php';
require_once __DIR__ . '/../model/cart-model.php';
require_once __DIR__ . '/../../common/model/model-common.php';
require_once __DIR__ . '/../../../enums/CurrencyEnum.php';
require_once __DIR__ . '/../../../enums/CampaignTypeEnum.php';
require_once __DIR__ . '/../../../enums/ErrorCodeEnum.php';

function processInteriorDoorsConfiguratorPost(array $requestBody, string $lang): PriceOfferResponse
{
    try {
        $parsedObject = new ConfiguratorPostRequest($requestBody);
        $priceOffer = isset($_SESSION['priceOffer'])
            ? PriceOffer::fromSession($_SESSION['priceOffer'])
            : new PriceOffer($lang);
        $_SESSION['priceOffer'] = $priceOffer->fromPostConfigurator($parsedObject);

        return $priceOffer->toResponse(
            CurrencyEnum::EUR,
            getActiveCampaignsOfType(
                CampaignTypeEnum::DOORS,
                new DateTime(),
                AppConfigJsonDataManipulation::getAll()['campaignDisabled']
                    ? array()
                    : CampaignsJsonDataManipulation::getAll()
            )
        );
    } catch (Throwable $exception) {
        $route = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: null;
        error_log($exception);
        createErrorLogRecord(
            getDbConnection(),
            $_SESSION['session_ref'] ?? null,
            ErrorCodeEnum::INTERIOR_DOORS_CONFIGURATOR_POST_FAILED,
            $exception->getMessage(),
            'configurator',
            $exception->__toString(),
            $route
        );

        throw $exception;
    }
}

function mapHandlesToResponseInteriorDoors(?string $currencyId): array
{
    $handles = HandlesJsonDataManipulation::getAll();
    return array_map(function ($handle) use ($currencyId) {
        return new HandleConfiguratorResponse(
            $handle['availableForDoorCategory'] ?? null,
            $handle['hiddenForDoorCategory'] ?? null,
            $handle['imgSrc'],
            $handle['label'],
            $handle['labelKey'],
            currencyRateTransformation($currencyId, $handle['price'], null)
        );
    }, $handles);
}

function mapDoorCategoriesToResponse(?string $currencyId): array
{
    $doorCategories = DoorCategoriesJsonDataManipulation::getAll();
    return array_map(function ($category) use ($currencyId) {
        return new DoorCategoryResponse(
            $category['categoryId'],
            $category['constructionPossibilities'],
            $category['description'],
            $category['descriptionKey'],
            $category['doorConfiguratorDefault'],
            mapDoorTypesToResponse($category['doorTypes'], $currencyId),
            $category['excludedDoorPartsFromCanvas'],
            $category['gallery'],
            $category['hiddenConfiguratorSections'],
            $category['name'],
            $category['nameKey']
        );
    }, $doorCategories);
}

function mapDoorTypesToResponse(array $doorTypes, ?string $currencyId): array
{
    $result = [];

    foreach ($doorTypes as $key => $doorType) {
        $price = $doorType['price'] ?? null;

        if (is_numeric($price)) {
            $doorType['price'] = currencyRateTransformation($currencyId, $price, null);
        }

        $result[$key] = $doorType;
    }

    return $result;
}