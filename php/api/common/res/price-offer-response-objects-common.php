<?php

class AddressResponse implements JsonSerializable
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

    public function __construct(
        ?string $city,
        ?string $deliveryCountry,
        ?string $district,
        ?bool   $isDeliveryCountryDirty,
        ?string $street,
        ?string $streetNumber,
        ?string $zipCode
    )
    {
        $this->city = $city;
        $this->deliveryCountry = $deliveryCountry;
        $this->district = $district;
        $this->isDeliveryCountryDirty = $isDeliveryCountryDirty;
        $this->street = $street;
        $this->streetNumber = $streetNumber;
        $this->zipCode = $zipCode;
    }

    public static function empty(): AddressResponse
    {
        return new AddressResponse(null, null, null, null, null, null, null);
    }

    public function jsonSerialize(): array
    {
        return [
            'city' => $this->city,
            'deliveryCountry' => $this->deliveryCountry,
            'district' => $this->district,
            'isDeliveryCountryDirty' => $this->isDeliveryCountryDirty,
            'street' => $this->street,
            'streetNumber' => $this->streetNumber,
            'zipCode' => $this->zipCode
        ];
    }
}

class ContactResponse implements JsonSerializable
{
    /** @var string|null $email */
    public $email;

    /** @var string|null $fullName */
    public $fullName;

    /** @var string|null $phoneNumber */
    public $phoneNumber;

    public function __construct(?string $email, ?string $fullName, ?string $phoneNumber)
    {
        $this->email = $email;
        $this->fullName = $fullName;
        $this->phoneNumber = $phoneNumber;
    }

    public static function empty(): ContactResponse
    {
        return new ContactResponse(null, null, null);
    }

    public function jsonSerialize(): array
    {
        return [
            'email' => $this->email,
            'fullName' => $this->fullName,
            'phoneNumber' => $this->phoneNumber
        ];
    }
}

class HandleResponse implements JsonSerializable
{
    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var int|null $count */
    public $count;

    /** @var string|null $id */
    public $id;

    /** @var bool|null $isCountDirty */
    public $isCountDirty;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    public function __construct(float $calculatedPrice, ?int $count, ?string $id, ?bool $isCountDirty, ?string $name, ?float $price)
    {
        $this->calculatedPrice = $calculatedPrice;
        $this->count = $count;
        $this->id = $id;
        $this->isCountDirty = $isCountDirty;
        $this->name = $name;
        $this->price = $price;
    }

    public function jsonSerialize(): array
    {
        return [
            'calculatedPrice' => $this->calculatedPrice,
            'count' => $this->count,
            'id' => $this->id,
            'isCountDirty' => $this->isCountDirty,
            'name' => $this->name,
            'price' => $this->price
        ];
    }
}