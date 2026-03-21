<?php
include_once "functions.php";
include_once "constants.php";

class DistrictsJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/districts.json");
    }

    public static function findByIdOrFalse(?string $id)
    {
        $list = DistrictsJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item);
    }
}

class DoorsJsonDataManipulation
{
    public static function getAllByCategory($category): array
    {
        global $doors_path;
        return getArrayFromJsonFile($doors_path . "/" . $category . "/price.json");
    }

    public static function getMaterialTranslation(string $translationKey)
    {
        return getArrayFromJsonFile("images/materials/conf.json")["Druhy laminátov"][$translationKey];
    }
}

class TechnicalSurchargesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/technicalSurcharges.json");
    }

    public static function findByIdOrFalse(?string $id)
    {
        $list = TechnicalSurchargesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item);
    }
}

class RosettesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/rosettes.json");
    }

    public static function findByIdOrFalse(?string $id)
    {
        $list = RosettesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item);
    }
}

class AestheticAccessoriesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/aestheticAccessories.json");
    }

    public static function findByIdOrFalse(?string $id)
    {
        $list = AestheticAccessoriesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item);
    }
}

class SpecialSurchargesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/specialSurcharges.json");
    }

    public static function findByIdOrFalse(?string $id)
    {
        $list = SpecialSurchargesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item);
    }
}

class AppConfigJsonDataManipulation
{
    public static function getAll(): array
    {
        $json = getArrayFromJsonFile("./app-config.json");
        $profile = $json["profile"];
        return $json[$profile];
    }
}

//Configurator
class DoorCategoriesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/doorCategories.json");
    }
}

class GalleryJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/gallery.json");
    }
}

class GlassesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/glasses.json");
    }
}

class HandlesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/handles.json");
    }

    public static function findByIdOrFalse(?string $id)
    {
        $obj = HandlesJsonDataManipulation::getAll();
        return $obj[$id] ?? false;
    }
}

class MaterialsJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/materials.json");
    }
}

class RoomsJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/rooms.json");
    }
}
?>