<?php

require_once __DIR__ . '/../../common/res/price-offer-response-objects-common.php';

class SecurityDoorsDoorResponse implements JsonSerializable
{
    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var string|null $frameColor */
    public $frameColor;

    /** @var string|null $material */
    public $material;

    /** @var string|null $type */
    public $type;

    /** @var string|null $width */
    public $width;

    public function __construct(
        float   $calculatedPrice,
        ?string $frameColor,
        ?string $material,
        ?string $type,
        ?string $width
    )
    {
        $this->calculatedPrice = $calculatedPrice;
        $this->frameColor = $frameColor;
        $this->material = $material;
        $this->type = $type;
        $this->width = $width;
    }

    public function jsonSerialize(): array
    {
        return [
            'calculatedPrice' => $this->calculatedPrice,
            'frameColor' => $this->frameColor,
            'material' => $this->material,
            'type' => $this->type,
            'width' => $this->width
        ];
    }
}


class SecurityDoorsCalculatedPriceResponse implements JsonSerializable
{
    /** @var float $doors */
    public $doors;

    /** @var float $handlesAndRosettes */
    public $handlesAndRosettes;

    /** @var float $delivery */
    public $delivery;

    /** @var float $assemblyDoors */
    public $assemblyDoors;

    public function __construct(
        float $doors,
        float $handlesAndRosettes,
        float $delivery,
        float $assemblyDoors
    )
    {
        $this->doors = $doors;
        $this->handlesAndRosettes = $handlesAndRosettes;
        $this->delivery = $delivery;
        $this->assemblyDoors = $assemblyDoors;
    }

    public function jsonSerialize(): array
    {
        return [
            'doors' => $this->doors,
            'handlesAndRosettes' => $this->handlesAndRosettes,
            'delivery' => $this->delivery,
            'assemblyDoors' => $this->assemblyDoors
        ];
    }
}

class SecurityDoorsPriceOfferResponse implements JsonSerializable
{
    /** @var AddressResponse $address */
    public $address;

    /** @var float $assemblyDoorsCalculatedPrice */
    public $assemblyDoorsCalculatedPrice;

    /** @var float|null $assemblyDoorsCost */
    public $assemblyDoorsCost;

    /** @var int|null $assemblyDoorsCount */
    public $assemblyDoorsCount;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var float $calculatedPriceVat */
    public $calculatedPriceVat;

    /** @var ContactResponse $contact */
    public $contact;

    /** @var string $currency */
    public $currency;

    /** @var float $deliveryPrice */
    public $deliveryPrice;

    /** @var float $deliveryPricePerKm */
    public $deliveryPricePerKm;

    /** @var SecurityDoorsDoorResponse[] $doors */
    public $doors;

    /** @var HandleResponse|null $handle */
    public $handle;

    /** @var bool|null $isAssemblyDoorsCountDirty */
    public $isAssemblyDoorsCountDirty;

    /** @var string|null $note */
    public $note;

    /** @var SecurityDoorsCalculatedPriceResponse $sectionsCalculatedPrice */
    public $sectionsCalculatedPrice;

    public function __construct(
        AddressResponse                      $address,
        float                                $assemblyDoorsCalculatedPrice,
        ?float                               $assemblyDoorsCost,
        ?int                                 $assemblyDoorsCount,
        float                                $calculatedPrice,
        float                                $calculatedPriceVat,
        ContactResponse                      $contact,
        string                               $currency,
        float                                $deliveryPrice,
        float                                $deliveryPricePerKm,
        array                                $doors,
        ?HandleResponse                      $handle,
        ?bool                                $isAssemblyDoorsCountDirty,
        ?string                              $note,
        SecurityDoorsCalculatedPriceResponse $sectionsCalculatedPrice
    )
    {
        $this->address = $address;
        $this->assemblyDoorsCalculatedPrice = $assemblyDoorsCalculatedPrice;
        $this->assemblyDoorsCost = $assemblyDoorsCost;
        $this->assemblyDoorsCount = $assemblyDoorsCount;
        $this->calculatedPrice = $calculatedPrice;
        $this->calculatedPriceVat = $calculatedPriceVat;
        $this->contact = $contact;
        $this->currency = $currency;
        $this->deliveryPrice = $deliveryPrice;
        $this->deliveryPricePerKm = $deliveryPricePerKm;
        $this->doors = $doors;
        $this->handle = $handle;
        $this->isAssemblyDoorsCountDirty = $isAssemblyDoorsCountDirty;
        $this->note = $note;
        $this->sectionsCalculatedPrice = $sectionsCalculatedPrice;
    }

    public function jsonSerialize(): array
    {
        return [
            'address' => $this->address,
            'assemblyDoorsCalculatedPrice' => $this->assemblyDoorsCalculatedPrice,
            'assemblyDoorsCost' => $this->assemblyDoorsCost,
            'assemblyDoorsCount' => $this->assemblyDoorsCount,
            'calculatedPrice' => $this->calculatedPrice,
            'calculatedPriceVat' => $this->calculatedPriceVat,
            'contact' => $this->contact,
            'currency' => $this->currency,
            'deliveryPrice' => $this->deliveryPrice,
            'deliveryPricePerKm' => $this->deliveryPricePerKm,
            'doors' => $this->doors,
            'handle' => $this->handle,
            'isAssemblyDoorsCountDirty' => $this->isAssemblyDoorsCountDirty,
            'note' => $this->note,
            'sectionsCalculatedPrice' => $this->sectionsCalculatedPrice
        ];
    }
}

class SecurityDoorsPriceOfferViewResponse implements JsonSerializable
{
    /** @var array $deliveryCountries */
    public $deliveryCountries;

    /** @var array $districts */
    public $districts;

    /** @var SecurityDoorsPriceOfferResponse $priceOffer */
    public $priceOffer;

    public function __construct(
        array                           $deliveryCountries,
        array                           $districts,
        SecurityDoorsPriceOfferResponse $priceOffer
    )
    {
        $this->deliveryCountries = $deliveryCountries;
        $this->districts = $districts;
        $this->priceOffer = $priceOffer;
    }

    public function jsonSerialize(): array
    {
        return [
            'deliveryCountries' => $this->deliveryCountries,
            'districts' => $this->districts,
            'priceOffer' => $this->priceOffer
        ];
    }
}