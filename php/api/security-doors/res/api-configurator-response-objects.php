<?php

class SecurityDoorsDoorCategoryResponse
{
    /** @var string $categoryId */
    public $categoryId;

    /** @var array $doorConfiguratorDefault */
    public $doorConfiguratorDefault;

    /** @var array $doorTypes */
    public $doorTypes;

    /** @var string $name */
    public $name;

    /** @var string $nameKey */
    public $nameKey;

    public function __construct(
        string $categoryId,
        array  $doorConfiguratorDefault,
        array  $doorTypes,
        string $name,
        string $nameKey
    )
    {
        $this->categoryId = $categoryId;
        $this->doorConfiguratorDefault = $doorConfiguratorDefault;
        $this->doorTypes = $doorTypes;
        $this->name = $name;
        $this->nameKey = $nameKey;
    }
}

class SecurityDoorsEquipmentOptionalResponse
{
    /** @var string $id */
    public $id;

    /** @var string $imgSrc */
    public $imgSrc;

    /** @var string $labelKey */
    public $labelKey;

    /** @var float $price */
    public $price;

    public function __construct(
        string $id,
        string $imgSrc,
        string $labelKey,
        float $price
    )
    {
        $this->id = $id;
        $this->imgSrc = $imgSrc;
        $this->labelKey = $labelKey;
        $this->price = $price;
    }
}

class SecurityDoorsHandleConfiguratorResponse
{
    /** @var string|null $imgSrc */
    public $imgSrc;

    /** @var string $label */
    public $label;

    /** @var string $labelKey */
    public $labelKey;

    /** @var float $price */
    public $price;

    public function __construct(
        ?string $imgSrc,
        string  $label,
        string  $labelKey,
        float   $price
    )
    {
        $this->imgSrc = $imgSrc;
        $this->label = $label;
        $this->labelKey = $labelKey;
        $this->price = $price;
    }
}

class SecurityDoorsConfiguratorResponse
{
    /** @var SecurityDoorsDoorCategoryResponse[] $doorCategories */
    public $doorCategories;

    /** @var array $equipmentIncluded */
    public $equipmentIncluded;

    /** @var SecurityDoorsEquipmentOptionalResponse[] $equipmentOptional */
    public $equipmentOptional;

    /** @var array $frameColors */
    public $frameColors;

    /** @var SecurityDoorsHandleConfiguratorResponse[] $handles */
    public $handles;

    /** @var array $materials */
    public $materials;

    /** @var array $rooms */
    public $rooms;

    public function __construct(
        array $doorCategories,
        array $equipmentIncluded,
        array $equipmentOptional,
        array $frameColors,
        array $handles,
        array $materials,
        array $rooms
    )
    {
        $this->doorCategories = $doorCategories;
        $this->equipmentIncluded = $equipmentIncluded;
        $this->equipmentOptional = $equipmentOptional;
        $this->frameColors = $frameColors;
        $this->handles = $handles;
        $this->materials = $materials;
        $this->rooms = $rooms;
    }
}