<?php
require_once __DIR__ . '/json-data-manipulation-facade.php';
require_once __DIR__ . '/response-objects/api-configurator-response-objects.php';

function mapHandlesToResponse(?string $currencyId): array
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