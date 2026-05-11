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

class DistrictsJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile(__DIR__ . "/../../assets/json/districts.json");
    }

    public static function findByIdOrNull(?string $id)
    {
        $list = DistrictsJsonDataManipulation::getAll();
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
        return $json[$profile];
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

    public static function getTranslation(string $id)
    {
        $json = getArrayFromJsonFile(__DIR__ . "/../../assets/json/materials.json");
        if (array_key_exists($id, $json["laminates"])) {
            return $json["laminates"][$id]["label"];
        } else if (array_key_exists($id, $json["veneers"])) {
            return $json["veneers"][$id]["label"];
        } else {
            return $id;
        }
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