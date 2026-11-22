<?php

interface Product
{
    public function getBasePriceFromDb(?array $dbRecord): ?float;
}

class Address
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

    public function __construct(?string $lang)
    {
        $this->city = null;
        $this->deliveryCountry = $this->getDeliveryCountry(null, $lang);
        $this->district = null;
        $this->isDeliveryCountryDirty = null;
        $this->street = null;
        $this->streetNumber = null;
        $this->zipCode = null;
    }

    public static function fromRequest(?AddressRequest $req, ?string $lang): Address
    {
        $instance = new self($lang);

        if ($req == null) {
            return $instance;
        }

        $instance->isDeliveryCountryDirty = $req->isDeliveryCountryDirty;
        $instance->deliveryCountry = $instance->getDeliveryCountry($req->deliveryCountry, $lang);
        $instance->city = $req->city;
        $instance->district = $req->district;
        $instance->street = $req->street;
        $instance->streetNumber = $req->streetNumber;
        $instance->zipCode = $req->zipCode;

        return $instance;
    }

    public function toResponse(): AddressResponse
    {
        return new AddressResponse(
            $this->city,
            $this->deliveryCountry,
            $this->district,
            $this->isDeliveryCountryDirty,
            $this->street,
            $this->streetNumber,
            $this->zipCode
        );
    }

    function getDeliveryCountry(?string $valueToBe, ?string $lang): ?string
    {
        if ($this->isDeliveryCountryDirty || $lang == null) {
            return $valueToBe;
        }

        switch ($lang) {
            case SupportedLanguagesEnum::CS:
                return "cz";
            case SupportedLanguagesEnum::DE:
                return "at";
            default:
                return "sk";
        }
    }
}

class Contact
{
    /** @var string|null $email */
    public $email;

    /** @var string|null $fullName */
    public $fullName;

    /** @var string|null $phoneNumber */
    public $phoneNumber;

    public function __construct()
    {
        $this->email = null;
        $this->fullName = null;
        $this->phoneNumber = null;
    }

    public static function fromRequest(?ContactRequest $req): Contact
    {
        $instance = new self();

        if ($req == null) {
            return $instance;
        }

        $instance->email = $req->email;
        $instance->fullName = $req->fullName;
        $instance->phoneNumber = $req->phoneNumber;

        return $instance;
    }

    public function toResponse(): ContactResponse
    {
        return new ContactResponse(
            $this->email,
            $this->fullName,
            $this->phoneNumber
        );
    }
}

class Handle implements Product
{
    /** @var int|null $count */
    public $count;

    /** @var string|null $id */
    public $id;

    /** @var bool|null $isCountDirty */
    public $isCountDirty;

    public function __construct()
    {
        $this->count = null;
        $this->id = null;
        $this->isCountDirty = null;
    }

    public static function fromRequest(HandleRequest $req): Handle
    {
        $instance = new self();
        $instance->count = $req->count;
        $instance->isCountDirty = $req->isCountDirty;
        $instance->id = $req->id;

        return $instance;
    }

    public static function fromConfiguratorPost(int $count, ?bool $isCountDirty, string $id): Handle
    {
        $instance = new self();
        $instance->count = $count;
        $instance->id = $id;
        $instance->isCountDirty = $isCountDirty;

        return $instance;
    }

    public function toResponse(?array $db, int $doorsCount, ?string $currencyId): HandleResponse
    {
        $effectiveCount = $this->getEffectiveCount($doorsCount);
        $basePriceDb = $this->getBasePriceFromDb($db) ?? 0.0;
        $currencyPrice = currencyRateTransformation($currencyId, $basePriceDb, null);

        return new HandleResponse(
            $this->calculatePrice($effectiveCount, $currencyId),
            $effectiveCount,
            $this->id,
            $this->isCountDirty,
            array_key_exists("label", $db) ? $db["label"] : null,
            $currencyPrice
        );
    }

    function calculatePrice(int $effectiveCount, ?string $currencyId): float
    {
        $db = HandlesJsonDataManipulation::findByIdOrNull($this->id);
        $basePrice = $this->getBasePriceFromDb($db) ?? 0.0;
        $currencyPrice = currencyRateTransformation($currencyId, $basePrice, null);
        return $effectiveCount * $currencyPrice;
    }

    function getEffectiveCount(int $doorsCount): int
    {
        if (!$this->isCountDirty) {
            return $doorsCount;
        }

        return $this->count ?? 0;
    }

    // Product
    public function getBasePriceFromDb(?array $dbRecord): ?float
    {
        $price = $dbRecord['price'] ?? null;

        if (!is_numeric($price)) {
            return null;
        }

        return (float)$price;
    }
}

function currencyRateTransformation(
    ?string $currencyId,
    float   $price,
    ?array  $priceCurrencyOverride
): float
{
    if ($priceCurrencyOverride && array_key_exists($currencyId, $priceCurrencyOverride)) {
        return (float)$priceCurrencyOverride[$currencyId];
    }

    $currencyRate = CurrencyRatesJsonDataManipulation::findByCurrencyOrNull($currencyId);
    return $currencyRate ? ($price * $currencyRate) : $price;
}