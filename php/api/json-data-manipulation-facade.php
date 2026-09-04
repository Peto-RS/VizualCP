<?php
require_once __DIR__ . "/../../functions.php";

class CampaignsJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/campaigns.json");
    }
}

class CurrencyRatesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/currencyRatesEur.json");
    }

    public static function findByCurrencyOrNull(?string $currency): ?float
    {
        $list = CurrencyRatesJsonDataManipulation::getAll();
        return (array_key_exists($currency, $list) && is_float($list[$currency])) ? $list[$currency] : null;
    }
}

class DeliveryCountriesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/countries-delivery.json");
    }
}

class DistrictsJsonDataManipulation
{
    public static function getAllByCountry(?string $countryId): array
    {
        switch ($countryId) {
            case "at":
                return getArrayFromJsonFile(__DIR__ . "/../../assets/json/districts-at.json");
            case "cz":
                return getArrayFromJsonFile(__DIR__ . "/../../assets/json/districts-cz.json");
            default:
                return getArrayFromJsonFile(__DIR__ . "/../../assets/json/districts.json");
        }
    }

    public static function findByIdAndCountryOrNull(?string $id, ?string $country)
    {
        $list = DistrictsJsonDataManipulation::getAllByCountry($country);
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item) ?? null;
    }
}

class DoorsJsonDataManipulation
{
    public static function getAllByCategory($category): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../images/doors/" . $category . "/price.json");
    }
}

class TechnicalSurchargesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/technicalSurcharges.json");
    }

    public static function findByIdOrNull(?string $id)
    {
        $list = TechnicalSurchargesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item) ?? null;
    }
}

class RosettesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/rosettes.json");
    }

    public static function findByIdOrNull(?string $id)
    {
        $list = RosettesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item) ?? null;
    }
}

class AestheticAccessoriesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/aestheticAccessories.json");
    }

    public static function findByIdOrNull(?string $id)
    {
        $list = AestheticAccessoriesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item) ?? null;
    }
}

class SpecialSurchargesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/specialSurcharges.json");
    }

    public static function findByIdOrNull(?string $id)
    {
        $list = SpecialSurchargesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item) ?? null;
    }
}

class AppConfigJsonDataManipulation
{
    public static function getAll(): array
    {
        $json = getArrayFromJsonFile(__DIR__ . "/../../app-config.json");
        $profile = $json["profile"];
        $response = $json[$profile];
        $response["profile"] = $profile;
        return $response;
    }
}

class ParametersJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/parameters.json");
    }
}

//Configurator
class DoorCategoriesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/doorCategories.json");
    }
}

class GalleryJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/gallery.json");
    }
}

class GlassesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/glasses.json");
    }
}

class HandlesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/handles.json");
    }

    public static function findByIdOrNull(?string $id)
    {
        $obj = HandlesJsonDataManipulation::getAll();
        return $obj[$id] ?? null;
    }
}

class MaterialsJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/materials.json");
    }
}

class RoomsJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/rooms.json");
    }
}

?>