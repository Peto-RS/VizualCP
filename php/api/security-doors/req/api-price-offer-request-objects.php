<?php

require_once __DIR__ . '/../../common/req/price-offer-request-objects-common.php';

class SecurityDoorsDoorRequest
{
    /** @var string|null $frameColor */
    public $frameColor;

    /** @var string|null $material */
    public $material;

    /** @var string|null $type */
    public $type;

    /** @var string|null $width */
    public $width;

    public function __construct(array $json)
    {
        $this->frameColor = $json['frameColor'] ?? null;
        $this->material = $json['material'] ?? null;
        $this->type = $json['type'] ?? null;
        $this->width = $json['width'] ?? null;
    }
}

class SecurityDoorsPriceOfferRequest
{
    /** @var AddressRequest|null $address */
    public $address;

    /** @var int|null $assemblyDoorsCount */
    public $assemblyDoorsCount;

    /** @var ContactRequest|null $contact */
    public $contact;

    /** @var Currency|null $currency */
    public $currency;

    /** @var SecurityDoorsDoorRequest[]|null $doors */
    public $doors;

    /** @var HandleRequest|null $handle */
    public $handle;

    /** @var bool|null $isAssemblyDoorsCountDirty */
    public $isAssemblyDoorsCountDirty;

    /** @var string|null $note */
    public $note;

    public function __construct(array $json)
    {
        $this->address = $json['address'] ? new AddressRequest($json['address']) : null;
        $this->assemblyDoorsCount = $json['assemblyDoorsCount'] ?? null;
        $this->contact = $json['contact'] ? new ContactRequest($json['contact']) : null;
        $this->currency = $json['currency'] ?? null;
        $this->doors = is_array($json['doors']) ? array_map(function ($doorData) {
            return new SecurityDoorsDoorRequest($doorData);
        }, $json['doors']) : null;
        $this->handle = $json['handle'] ? new HandleRequest($json['handle']) : null;
        $this->isAssemblyDoorsCountDirty = $json['isAssemblyDoorsCountDirty'] ?? null;
        $this->note = $json['note'] ?? null;
    }
}

class SecurityDoorsPriceOfferApiRequest
{
    /** @var SecurityDoorsPriceOfferRequest|null $priceOffer */
    public $priceOffer;

    public function __construct(array $json)
    {
        $this->priceOffer = $json['priceOffer'] ? new SecurityDoorsPriceOfferRequest($json['priceOffer']) : null;
    }
}