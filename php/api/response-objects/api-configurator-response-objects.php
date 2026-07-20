<?php

class DoorCategoryResponse
{
    /** @var string $categoryId */
    public $categoryId;

    /** @var array $constructionPossibilities */
    public $constructionPossibilities;

    /** @var string $description */
    public $description;

    /** @var string $descriptionKey */
    public $descriptionKey;

    /** @var array $doorConfiguratorDefault */
    public $doorConfiguratorDefault;

    /** @var array $doorTypes */
    public $doorTypes;

    /** @var array $excludedDoorPartsFromCanvas */
    public $excludedDoorPartsFromCanvas;

    /** @var array $gallery */
    public $gallery;

    /** @var array $hiddenConfiguratorSections */
    public $hiddenConfiguratorSections;

    /** @var string $name */
    public $name;

    /** @var string $nameKey */
    public $nameKey;

    public function __construct(
        string $categoryId,
        array  $constructionPossibilities,
        string $description,
        string $descriptionKey,
        array  $doorConfiguratorDefault,
        array  $doorTypes,
        array  $excludedDoorPartsFromCanvas,
        array  $gallery,
        array  $hiddenConfiguratorSections,
        string $name,
        string $nameKey
    )
    {
        $this->categoryId = $categoryId;
        $this->constructionPossibilities = $constructionPossibilities;
        $this->description = $description;
        $this->descriptionKey = $descriptionKey;
        $this->doorConfiguratorDefault = $doorConfiguratorDefault;
        $this->doorTypes = $doorTypes;
        $this->excludedDoorPartsFromCanvas = $excludedDoorPartsFromCanvas;
        $this->gallery = $gallery;
        $this->hiddenConfiguratorSections = $hiddenConfiguratorSections;
        $this->name = $name;
        $this->nameKey = $nameKey;
    }
}

class HandleConfiguratorResponse
{
    /** @var array|null $availableForDoorCategory */
    public $availableForDoorCategory;

    /** @var array|null $hiddenForDoorCategory */
    public $hiddenForDoorCategory;

    /** @var string|null $imgSrc */
    public $imgSrc;

    /** @var string $label */
    public $label;

    /** @var string $labelKey */
    public $labelKey;

    /** @var float $price */
    public $price;

    public function __construct(
        ?array  $availableForDoorCategory,
        ?array  $hiddenForDoorCategory,
        ?string $imgSrc,
        string  $label,
        string  $labelKey,
        float   $price
    )
    {
        $this->availableForDoorCategory = $availableForDoorCategory ?? null;
        $this->hiddenForDoorCategory = $hiddenForDoorCategory ?? null;
        $this->imgSrc = $imgSrc;
        $this->label = $label;
        $this->labelKey = $labelKey;
        $this->price = $price;
    }
}

class ConfiguratorResponse
{
    /** @var float $assemblyDoorsCost */
    public $assemblyDoorsCost;

    /** @var DoorCategoryResponse[] $doorCategories */
    public $doorCategories;

    /** @var float $framePrice */
    public $framePrice;

    /** @var array $gallery */
    public $gallery;

    /** @var array $glasses */
    public $glasses;

    /** @var array $handles */
    public $handles;

    /** @var float $interiorDoorsPriceFrom */
    public $interiorDoorsPriceFrom;

    /** @var array $materials */
    public $materials;

    /** @var array $rooms */
    public $rooms;

    public function __construct(
        float $assemblyDoorsCost,
        array $doorCategories,
        float $framePrice,
        array $gallery,
        array $glasses,
        array $handles,
        float $interiorDoorsPriceFrom,
        array $materials,
        array $rooms
    ) {
        $this->assemblyDoorsCost = $assemblyDoorsCost;
        $this->doorCategories = $doorCategories;
        $this->framePrice = $framePrice;
        $this->gallery = $gallery;
        $this->glasses = $glasses;
        $this->handles = $handles;
        $this->interiorDoorsPriceFrom = $interiorDoorsPriceFrom;
        $this->materials = $materials;
        $this->rooms = $rooms;
    }
}