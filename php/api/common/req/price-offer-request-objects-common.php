<?php

class AddressRequest
{
    /** @var string|null $city */
    public $city;

    /** @var string|null $deliveryCountry */
    public $deliveryCountry;

    /** @var string|null $district */
    public $district;

    /** @var bool|null $isDeliveryCountryDirty */
    public $isDeliveryCountryDirty;

    /** @var string|null $street */
    public $street;

    /** @var string|null $streetNumber */
    public $streetNumber;

    /** @var string|null $zipCode */
    public $zipCode;

    public function __construct(array $json)
    {
        $this->city = $json['city'] ?? null;
        $this->deliveryCountry = $json['deliveryCountry'] ?? null;
        $this->district = $json['district'] ?? null;
        $this->isDeliveryCountryDirty = $json['isDeliveryCountryDirty'] ?? null;
        $this->street = $json['street'] ?? null;
        $this->streetNumber = $json['streetNumber'] ?? null;
        $this->zipCode = $json['zipCode'] ?? null;
    }
}

class ContactRequest
{
    /** @var string|null $email */
    public $email;

    /** @var string|null $fullName */
    public $fullName;

    /** @var string|null $phoneNumber */
    public $phoneNumber;

    public function __construct(array $json)
    {
        $this->email = $json['email'] ?? null;
        $this->fullName = $json['fullName'] ?? null;
        $this->phoneNumber = $json['phoneNumber'] ?? null;
    }
}

class HandleRequest
{
    /** @var int|null $count */
    public $count;

    /** @var string|null $id */
    public $id;

    /** @var bool|null $isCountDirty */
    public $isCountDirty;

    public function __construct(array $json)
    {
        $this->count = $json['count'] ?? null;
        $this->id = $json['id'] ?? null;
        $this->isCountDirty = $json['isCountDirty'] ?? null;
    }
}