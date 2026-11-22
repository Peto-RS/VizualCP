<?php

class SecurityDoorsConfiguratorPostRequest
{
    /** @var string $frameColor */
    public $frameColor;

    /** @var string $handleId */
    public $handleId;

    /** @var string|null $material */
    public $material;

    /** @var string|null $type */
    public $type;

    /** @var string|null $width */
    public $width;

    public function __construct(array $json)
    {
        $this->frameColor = $json['frameColor'] ?? null;
        $this->handleId = $json['handleId'] ?? null;
        $this->material = $json['material'] ?? null;
        $this->type = $json['type'] ?? null;
        $this->width = $json['width'] ?? null;
    }
}