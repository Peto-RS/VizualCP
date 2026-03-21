<?php

class ConfiguratorResponse
{
    /** @var array $doorCategories */
    public $doorCategories;

    /** @var array $gallery */
    public $gallery;

    /** @var array $glasses */
    public $glasses;

    /** @var array $handles */
    public $handles;

    /** @var array $materials */
    public $materials;

    /** @var array $rooms */
    public $rooms;

    public function __construct(array $doorCategories, array $gallery, array $glasses, array $handles, array $materials, array $rooms)
    {
        $this->doorCategories = $doorCategories;
        $this->gallery = $gallery;
        $this->glasses = $glasses;
        $this->handles = $handles;
        $this->materials = $materials;
        $this->rooms = $rooms;
    }
}
?>