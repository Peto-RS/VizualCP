<?php
require_once __DIR__ . '/../../common/facade/json-data-manipulation-facade.php';
require_once __DIR__ . '/../../common/facade/db-facade.php';
require_once __DIR__ . '/../../interior-doors/facade/configurator-facade.php';
require_once __DIR__ . '/../req/api-configurator-request-objects.php';
require_once __DIR__ . '/../res/api-configurator-response-objects.php';
require_once __DIR__ . '/../model/model-security-doors.php';
require_once __DIR__ . '/../../../enums/CurrencyEnum.php';
require_once __DIR__ . '/../../../enums/ErrorCodeEnum.php';

function processSecurityDoorsConfiguratorPost(array $requestBody, string $lang): SecurityDoorsPriceOfferResponse
{
    try {
        $parsedObject = new SecurityDoorsConfiguratorPostRequest($requestBody);
        $priceOffer = isset($_SESSION['securityDoorsPriceOffer'])
            ? SecurityDoorsPriceOffer::fromSession($_SESSION['securityDoorsPriceOffer'])
            : new SecurityDoorsPriceOffer($lang);
        $_SESSION['securityDoorsPriceOffer'] = $priceOffer->fromPostConfigurator($parsedObject);

        return $priceOffer->toResponse(CurrencyEnum::EUR);
    } catch (Throwable $exception) {
        $route = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: null;
        error_log($exception);
        createErrorLogRecord(
            getDbConnection(),
            $_SESSION['session_ref'] ?? null,
            ErrorCodeEnum::SECURITY_DOORS_CONFIGURATOR_POST_FAILED,
            $exception->getMessage(),
            'configurator',
            $exception->__toString(),
            $route
        );

        throw $exception;
    }
}

function mapSecurityDoorCategoriesToResponse(?string $currencyId): array
{
    $doorCategories = SecurityDoorCategoriesJsonDataManipulation::getAll();
    return array_map(function ($category) use ($currencyId) {
        return new SecurityDoorsDoorCategoryResponse(
            $category['categoryId'],
            $category['doorConfiguratorDefault'],
            mapDoorTypesToResponse($category['doorTypes'], $currencyId),
            $category['name'],
            $category['nameKey']
        );
    }, $doorCategories);
}

function mapOptionalEquipmentToResponse(?string $currencyId): array
{
    $equipment = SecurityDoorEquipmentOptionalJsonDataManipulation::getAll();
    return array_map(function ($it) use ($currencyId) {
        return new SecurityDoorsEquipmentOptionalResponse(
            $it['id'] ?? null,
            $it['imgSrc'],
            $it['labelKey'],
            currencyRateTransformation($currencyId, $it['price'], null)
        );
    }, $equipment);
}