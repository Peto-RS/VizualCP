<?php

class SecurityDoorsTenantResponse
{
    /** @var string $slug */
    public $slug;

    /** @var string $name */
    public $name;

    public function __construct(string $slug, string $name)
    {
        $this->slug = $slug;
        $this->name = $name;
    }
}
