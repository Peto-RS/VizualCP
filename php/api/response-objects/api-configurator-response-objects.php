<?php

class ConfiguratorResponse
{
    /** @var array $doorCategories */
    public $doorCategories;

    /** @var array $glasses */
    public $glasses;

    /** @var array $handles */
    public $handles;

    /** @var array $materials */
    public $materials;

    /** @var array $rooms */
    public $rooms;

    public function __construct(array $doorCategories, array $glasses, array $handles, array $materials, array $rooms)
    {
        $this->doorCategories = $doorCategories;
        $this->glasses = $glasses;
        $this->handles = $handles;
        $this->materials = $materials;
        $this->rooms = $rooms;
    }
}
?>