<?php

class ConfiguratorPostRequest
{
    /** @var string|null $category */
    public $category;

    /** @var string $handleId */
    public $handleId;

    /** @var string|null $material */
    public $material;

    /** @var string|null $type */
    public $type;

    public function __construct(array $json)
    {
        $this->category = $json['category'] ?? null;
        $this->handleId = $json['handleId'] ?? null;
        $this->material = $json['material'] ?? null;
        $this->type = $json['type'] ?? null;
    }
}

?>