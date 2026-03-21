<?php

include_once('constants.php');
include_once('functions.php');

class AddressResponse implements JsonSerializable
{
    /** @var string|null $city */
    public $city;

    /** @var string|null $district */
    public $district;

    /** @var string|null $street */
    public $street;

    /** @var string|null $streetNumber */
    public $streetNumber;

    /** @var string|null $zipCode */
    public $zipCode;

    public function __construct(?string $city, ?string $district, ?string $street, ?string $streetNumber, ?string $zipCode)
    {
        $this->city = $city;
        $this->district = $district;
        $this->street = $street;
        $this->streetNumber = $streetNumber;
        $this->zipCode = $zipCode;
    }

    public static function empty(): AddressResponse
    {
        return new AddressResponse(null, null, null, null, null);
    }

    public function jsonSerialize(): array
    {
        return [
            'city' => $this->city,
            'district' => $this->district,
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

class DoorResponse implements JsonSerializable
{
    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var string|null $category */
    public $category;

    /** @var boolean|null $isDoorFrameEnabled */
    public $isDoorFrameEnabled;

    /** @var boolean $isDtdAvailable */
    public $isDtdAvailable;

    /** @var boolean|null $isDtdSelected */
    public $isDtdSelected;

    /** @var string|null $material */
    public $material;

    /** @var string|null $type */
    public $type;

    /** @var string|null $width */
    public $width;

    public function __construct(float   $calculatedPrice, ?string $category, ?bool $isDoorFrameEnabled,
                                bool    $isDtdAvailable, ?bool $isDtdSelected, ?string $material, ?string $type,
                                ?string $width)
    {
        $this->calculatedPrice = $calculatedPrice;
        $this->category = $category;
        $this->isDoorFrameEnabled = $isDoorFrameEnabled;
        $this->isDtdAvailable = $isDtdAvailable;
        $this->isDtdSelected = $isDtdSelected;
        $this->material = $material;
        $this->type = $type;
        $this->width = $width;
    }

    public function jsonSerialize(): array
    {
        return [
            'calculatedPrice' => $this->calculatedPrice,
            'category' => $this->category,
            'isDoorFrameEnabled' => $this->isDoorFrameEnabled,
            'isDtdAvailable' => $this->isDtdAvailable,
            'isDtdSelected' => $this->isDtdSelected,
            'material' => $this->material,
            'type' => $this->type,
            'width' => $this->width
        ];
    }
}

class SelectedDoorLineItemResponse implements JsonSerializable
{
    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var boolean|null $isDoorFrameEnabled */
    public $isDoorFrameEnabled;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    /** @var string|null $width */
    public $width;

    public function __construct(float   $calculatedPrice, ?bool $isDoorFrameEnabled, ?string $name, ?float $price,
                                ?string $width)
    {
        $this->calculatedPrice = $calculatedPrice;
        $this->isDoorFrameEnabled = $isDoorFrameEnabled;
        $this->name = $name;
        $this->price = $price;
        $this->width = $width;
    }

    public function jsonSerialize(): array
    {
        return [
            'calculatedPrice' => $this->calculatedPrice,
            'isDoorFrameEnabled' => $this->isDoorFrameEnabled,
            'name' => $this->name,
            'price' => $this->price,
            'width' => $this->width
        ];
    }
}

class CustomHandleResponse implements JsonSerializable
{
    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var int|null $count */
    public $count;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    public function __construct(float $calculatedPrice, ?int $count, ?string $name, ?float $price)
    {
        $this->calculatedPrice = $calculatedPrice;
        $this->count = $count;
        $this->name = $name;
        $this->price = $price;
    }

    public static function empty(): CustomHandleResponse
    {
        return new CustomHandleResponse(0, null, null, null);
    }

    public function jsonSerialize(): array
    {
        return [
            'calculatedPrice' => $this->calculatedPrice,
            'count' => $this->count,
            'name' => $this->name,
            'price' => $this->price
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

class LineItemResponse implements JsonSerializable
{
    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var int|null $count */
    public $count;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    public function __construct(float $calculatedPrice, ?int $count, ?string $name, ?float $price)
    {
        $this->calculatedPrice = $calculatedPrice;
        $this->count = $count;
        $this->name = $name;
        $this->price = $price;
    }

    public function jsonSerialize(): array
    {
        return [
            'calculatedPrice' => $this->calculatedPrice,
            'count' => $this->count,
            'name' => $this->name,
            'price' => $this->price
        ];
    }
}

class TechnicalSurchargeResponse implements JsonSerializable
{
    /** @var string $id */
    public $id;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var float|null $configuredPrice */
    public $configuredPrice;

    /** @var int|null $count */
    public $count;

    /** @var string|null $header */
    public $header;

    /** @var string|null $hint */
    public $hint;

    /** @var string|null $imgSrc */
    public $imgSrc;

    /** @var boolean|null $isCountDirty */
    public $isCountDirty;

    /** @var string|null $label */
    public $label;

    /** @var string|null $youtubeVideoCode */
    public $youtubeVideoCode;

    /** @var string|null $videoSrc */
    public $videoSrc;

    public function __construct(string  $id, float $calculatedPrice, ?float $configuredPrice, ?int $count,
                                ?string $isCountDirty, ?string $header, ?string $hint, ?string $imgSrc, ?string $label,
                                ?string $youtubeVideoCode, ?string $videoSrc)
    {
        $this->id = $id;
        $this->calculatedPrice = $calculatedPrice;
        $this->configuredPrice = $configuredPrice;
        $this->count = $count;
        $this->header = $header;
        $this->hint = $hint;
        $this->imgSrc = $imgSrc;
        $this->isCountDirty = $isCountDirty;
        $this->label = $label;
        $this->youtubeVideoCode = $youtubeVideoCode;
        $this->videoSrc = $videoSrc;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'calculatedPrice' => $this->calculatedPrice,
            'configuredPrice' => $this->configuredPrice,
            'count' => $this->count,
            'header' => $this->header,
            'hint' => $this->hint,
            'imgSrc' => $this->imgSrc,
            'isCountDirty' => $this->isCountDirty,
            'label' => $this->label,
            'youtubeVideoCode' => $this->youtubeVideoCode,
            'videoSrc' => $this->videoSrc
        ];
    }
}

class RosetteResponse implements JsonSerializable
{
    /** @var string $id */
    public $id;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var int|null $count */
    public $count;

    /** @var string|null $header */
    public $header;

    /** @var string|null $hint */
    public $hint;

    /** @var string|null $imgSrc */
    public $imgSrc;

    /** @var string|null $label */
    public $label;

    /** @var float|null $price */
    public $price;

    /** @var string|null $youtubeVideoCode */
    public $youtubeVideoCode;

    /** @var string|null $videoSrc */
    public $videoSrc;

    public function __construct(string  $id, float $calculatedPrice, ?int $count, ?string $header, ?string $hint,
                                ?string $imgSrc, ?string $label, ?float $price, ?string $youtubeVideoCode,
                                ?string $videoSrc)
    {
        $this->id = $id;
        $this->calculatedPrice = $calculatedPrice;
        $this->count = $count;
        $this->header = $header;
        $this->hint = $hint;
        $this->imgSrc = $imgSrc;
        $this->label = $label;
        $this->price = $price;
        $this->youtubeVideoCode = $youtubeVideoCode;
        $this->videoSrc = $videoSrc;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'calculatedPrice' => $this->calculatedPrice,
            'count' => $this->count,
            'header' => $this->header,
            'hint' => $this->hint,
            'imgSrc' => $this->imgSrc,
            'label' => $this->label,
            'price' => $this->price,
            'youtubeVideoCode' => $this->youtubeVideoCode,
            'videoSrc' => $this->videoSrc
        ];
    }
}

class SectionsCalculatedPriceResponse implements JsonSerializable
{
    /** @var float $doors */
    public $doors;

    /** @var float $handlesAndRosettes */
    public $handlesAndRosettes;

    /** @var float $delivery */
    public $delivery;

    /** @var float $assemblyDoors */
    public $assemblyDoors;

    /** @var float $aestheticAccessories */
    public $aestheticAccessories;

    /** @var float $technicalSurcharges */
    public $technicalSurcharges;

    /** @var float $specialSurcharges */
    public $specialSurcharges;

    public function __construct(float $doors, float $handlesAndRosettes, float $delivery, float $assemblyDoors,
                                float $aestheticAccessories, float $technicalSurcharges, float $specialSurcharges)
    {
        $this->doors = $doors;
        $this->handlesAndRosettes = $handlesAndRosettes;
        $this->delivery = $delivery;
        $this->assemblyDoors = $assemblyDoors;
        $this->aestheticAccessories = $aestheticAccessories;
        $this->technicalSurcharges = $technicalSurcharges;
        $this->specialSurcharges = $specialSurcharges;
    }

    public function jsonSerialize(): array
    {
        return [
            'doors' => $this->doors,
            'handlesAndRosettes' => $this->handlesAndRosettes,
            'delivery' => $this->delivery,
            'assemblyDoors' => $this->assemblyDoors,
            'aestheticAccessories' => $this->aestheticAccessories,
            'technicalSurcharges' => $this->technicalSurcharges,
            'specialSurcharges' => $this->specialSurcharges
        ];
    }
}

class AestheticAccessoryResponse implements JsonSerializable
{
    /** @var string $id */
    public $id;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var float|null $configuredPrice */
    public $configuredPrice;

    /** @var int|null $count */
    public $count;

    /** @var string|null $header */
    public $header;

    /** @var string|null $hint */
    public $hint;

    /** @var string|null $imgSrc */
    public $imgSrc;

    /** @var string|null $label */
    public $label;

    /** @var float|null $selectedPrice */
    public $selectedPrice;

    /** @var string|null $youtubeVideoCode */
    public $youtubeVideoCode;

    /** @var string|null $videoSrc */
    public $videoSrc;

    public function __construct(string  $id, float $calculatedPrice, ?float $configuredPrice, ?int $count, ?string $header,
                                ?string $hint, ?string $imgSrc, ?string $label, ?float $selectedPrice,
                                ?string $youtubeVideoCode, ?string $videoSrc)
    {
        $this->id = $id;
        $this->calculatedPrice = $calculatedPrice;
        $this->configuredPrice = $configuredPrice;
        $this->count = $count;
        $this->header = $header;
        $this->hint = $hint;
        $this->imgSrc = $imgSrc;
        $this->label = $label;
        $this->selectedPrice = $selectedPrice;
        $this->youtubeVideoCode = $youtubeVideoCode;
        $this->videoSrc = $videoSrc;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'calculatedPrice' => $this->calculatedPrice,
            'configuredPrice' => $this->configuredPrice,
            'count' => $this->count,
            'header' => $this->header,
            'hint' => $this->hint,
            'imgSrc' => $this->imgSrc,
            'label' => $this->label,
            'selectedPrice' => $this->selectedPrice,
            'youtubeVideoCode' => $this->youtubeVideoCode,
            'videoSrc' => $this->videoSrc
        ];
    }
}

class SpecialSurchargeResponse implements JsonSerializable
{
    /** @var string $id */
    public $id;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var float|null $configuredPrice */
    public $configuredPrice;

    /** @var int|null $count */
    public $count;

    /** @var string|null $header */
    public $header;

    /** @var string|null $hint */
    public $hint;

    /** @var string|null $imgSrc */
    public $imgSrc;

    /** @var boolean|null $isAssemblySelected */
    public $isAssemblySelected;

    /** @var boolean|null $isAssemblySelectedDirty */
    public $isAssemblySelectedDirty;

    /** @var string|null $label */
    public $label;

    /** @var string|null $labelAssembly */
    public $labelAssembly;

    /** @var string|null $youtubeVideoCode */
    public $youtubeVideoCode;

    /** @var string|null $videoSrc */
    public $videoSrc;

    public function __construct(string  $id, float $calculatedPrice, ?float $configuredPrice, ?int $count, ?string $header,
                                ?string $hint, ?string $imgSrc, ?bool $isAssemblySelected,
                                ?bool   $isAssemblySelectedDirty, ?string $label, ?string $labelAssembly,
                                ?string $youtubeVideoCode, ?string $videoSrc)
    {
        $this->id = $id;
        $this->calculatedPrice = $calculatedPrice;
        $this->configuredPrice = $configuredPrice;
        $this->count = $count;
        $this->header = $header;
        $this->hint = $hint;
        $this->imgSrc = $imgSrc;
        $this->isAssemblySelected = $isAssemblySelected;
        $this->isAssemblySelectedDirty = $isAssemblySelectedDirty;
        $this->label = $label;
        $this->labelAssembly = $labelAssembly;
        $this->youtubeVideoCode = $youtubeVideoCode;
        $this->videoSrc = $videoSrc;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'calculatedPrice' => $this->calculatedPrice,
            'configuredPrice' => $this->configuredPrice,
            'count' => $this->count,
            'header' => $this->header,
            'hint' => $this->hint,
            'imgSrc' => $this->imgSrc,
            'isAssemblySelected' => $this->isAssemblySelected,
            'isAssemblySelectedDirty' => $this->isAssemblySelectedDirty,
            'label' => $this->label,
            'labelAssembly' => $this->labelAssembly,
            'youtubeVideoCode' => $this->youtubeVideoCode,
            'videoSrc' => $this->videoSrc
        ];
    }
}

class PriceOfferResponse implements JsonSerializable
{
    /** @var AddressResponse $address */
    public $address;

    /** @var float $assemblyDoorsCalculatedPrice */
    public $assemblyDoorsCalculatedPrice;

    /** @var int|null $assemblyDoorsCount */
    public $assemblyDoorsCount;

    /** @var int|null $assemblyHandlesRosettesCount */
    public $assemblyHandlesRosettesCount;

    /** @var float $assemblyPriceHandlesRosettesCalculatedPrice */
    public $assemblyPriceHandlesRosettesCalculatedPrice;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var float $calculatedPriceVat */
    public $calculatedPriceVat;

    /** @var ContactResponse $contact */
    public $contact;

    /** @var float $deliveryPrice */
    public $deliveryPrice;

    /** @var DoorResponse[] $doors */
    public $doors;

    /** @var CustomHandleResponse $customHandle */
    public $customHandle;

    /** @var HandleResponse|null $handle */
    public $handle;

    /** @var bool|null $isAssemblyDoorsCountDirty */
    public $isAssemblyDoorsCountDirty;
    /** @var bool|null $isAssemblyHandlesRosettesCountDirty */
    public $isAssemblyHandlesRosettesCountDirty;

    /** @var string|null $note */
    public $note;

    /** @var TechnicalSurchargeResponse[] $technicalSurcharges */
    public $technicalSurcharges;

    /** @var LineItemResponse[] $technicalSurchargesLineItems */
    public $technicalSurchargesLineItems;

    /** @var RosetteResponse[] $rosettes */
    public $rosettes;

    /** @var LineItemResponse[] $rosettesLineItems */
    public $rosettesLineItems;

    /** @var SectionsCalculatedPriceResponse $sectionsCalculatedPrice */
    public $sectionsCalculatedPrice;

    /** @var SelectedDoorLineItemResponse[] $selectedDoorsLineItems */
    public $selectedDoorsLineItems;

    /** @var AestheticAccessoryResponse[] $aestheticAccessories */
    public $aestheticAccessories;

    /** @var LineItemResponse[] $aestheticAccessoriesLineItems */
    public $aestheticAccessoriesLineItems;

    /** @var SpecialSurchargeResponse[] $specialSurcharges */
    public $specialSurcharges;

    /** @var LineItemResponse[] $specialSurchargesLineItems */
    public $specialSurchargesLineItems;

    public function __construct(
        AddressResponse                 $address,
        float                           $assemblyDoorsCalculatedPrice,
        ?int                            $assemblyDoorsCount,
        ?int                            $assemblyHandlesRosettesCount,
        float                           $assemblyPriceHandlesRosettesCalculatedPrice,
        float                           $calculatedPrice,
        float                           $calculatedPriceVat,
        ContactResponse                 $contact,
        float                           $deliveryPrice,
        array                           $doors,
        CustomHandleResponse            $customHandle,
        ?HandleResponse                 $handle,
        ?bool                           $isAssemblyDoorsCountDirty,
        ?bool                           $isAssemblyHandlesRosettesCountDirty,
        ?string                         $note,
        array                           $technicalSurcharges,
        array                           $technicalSurchargesLineItems,
        array                           $rosettes,
        array                           $rosettesLineItems,
        SectionsCalculatedPriceResponse $sectionsCalculatedPrice,
        array                           $selectedDoorsLineItems,
        array                           $aestheticAccessories,
        array                           $aestheticAccessoriesLineItems,
        array                           $specialSurcharges,
        array                           $specialSurchargesLineItems
    )
    {
        $this->address = $address;
        $this->assemblyDoorsCalculatedPrice = $assemblyDoorsCalculatedPrice;
        $this->assemblyDoorsCount = $assemblyDoorsCount;
        $this->assemblyHandlesRosettesCount = $assemblyHandlesRosettesCount;
        $this->assemblyPriceHandlesRosettesCalculatedPrice = $assemblyPriceHandlesRosettesCalculatedPrice;
        $this->calculatedPrice = $calculatedPrice;
        $this->calculatedPriceVat = $calculatedPriceVat;
        $this->contact = $contact;
        $this->deliveryPrice = $deliveryPrice;
        $this->doors = $doors;
        $this->customHandle = $customHandle;
        $this->handle = $handle;
        $this->isAssemblyDoorsCountDirty = $isAssemblyDoorsCountDirty;
        $this->isAssemblyHandlesRosettesCountDirty = $isAssemblyHandlesRosettesCountDirty;
        $this->note = $note;
        $this->technicalSurcharges = $technicalSurcharges;
        $this->technicalSurchargesLineItems = $technicalSurchargesLineItems;
        $this->rosettes = $rosettes;
        $this->rosettesLineItems = $rosettesLineItems;
        $this->sectionsCalculatedPrice = $sectionsCalculatedPrice;
        $this->selectedDoorsLineItems = $selectedDoorsLineItems;
        $this->aestheticAccessories = $aestheticAccessories;
        $this->aestheticAccessoriesLineItems = $aestheticAccessoriesLineItems;
        $this->specialSurcharges = $specialSurcharges;
        $this->specialSurchargesLineItems = $specialSurchargesLineItems;
    }

    public function jsonSerialize(): array
    {
        return [
            'address' => $this->address,
            'assemblyDoorsCalculatedPrice' => $this->assemblyDoorsCalculatedPrice,
            'assemblyDoorsCount' => $this->assemblyDoorsCount,
            'assemblyHandlesRosettesCount' => $this->assemblyHandlesRosettesCount,
            'assemblyPriceHandlesRosettesCalculatedPrice' => $this->assemblyPriceHandlesRosettesCalculatedPrice,
            'calculatedPrice' => $this->calculatedPrice,
            'calculatedPriceVat' => $this->calculatedPriceVat,
            'contact' => $this->contact,
            'deliveryPrice' => $this->deliveryPrice,
            'doors' => $this->doors,
            'customHandle' => $this->customHandle,
            'handle' => $this->handle,
            'isAssemblyDoorsCountDirty' => $this->isAssemblyDoorsCountDirty,
            'isAssemblyHandlesRosettesCountDirty' => $this->isAssemblyHandlesRosettesCountDirty,
            'note' => $this->note,
            'technicalSurcharges' => $this->technicalSurcharges,
            'technicalSurchargesLineItems' => $this->technicalSurchargesLineItems,
            'rosettes' => $this->rosettes,
            'rosettesLineItems' => $this->rosettesLineItems,
            'sectionsCalculatedPrice' => $this->sectionsCalculatedPrice,
            'selectedDoorsLineItems' => $this->selectedDoorsLineItems,
            'aestheticAccessories' => $this->aestheticAccessories,
            'aestheticAccessoriesLineItems' => $this->aestheticAccessoriesLineItems,
            'specialSurcharges' => $this->specialSurcharges,
            'specialSurchargesLineItems' => $this->specialSurchargesLineItems
        ];
    }
}

class ApiResponse implements JsonSerializable
{
    /** @var array $districts */
    public $districts;

    /** @var PriceOfferResponse $priceOffer */
    public $priceOffer;

    public function __construct(array $districts, PriceOfferResponse $priceOffer)
    {
        $this->districts = $districts;
        $this->priceOffer = $priceOffer;
    }

    public function jsonSerialize(): array
    {
        return [
            'districts' => $this->districts,
            'priceOffer' => $this->priceOffer
        ];
    }
}

?>