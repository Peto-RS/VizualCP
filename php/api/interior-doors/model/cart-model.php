<?php

require_once __DIR__ . '/../facade/campaign-facade.php';
require_once __DIR__ . '/../../common/facade/json-data-manipulation-facade.php';
require_once __DIR__ . '/../req/api-price-offer-request-objects.php';
require_once __DIR__ . '/../res/api-configurator-response-objects.php';
require_once __DIR__ . '/../res/api-price-offer-response-objects.php';
require_once __DIR__ . '/../../../enums/CampaignTypeEnum.php';
require_once __DIR__ . '/../../../enums/CurrencyEnum.php';
require_once __DIR__ . '/../../../enums/DoorsWidthEnum.php';
require_once __DIR__ . '/../../common/model/model-common.php';

class Campaign
{
    /** @var string $id */
    public $id;

    /** @var float|null $percentageDiscount */
    public $percentageDiscount;

    /** @var string $translationKey */
    public $translationKey;

    /** @var string $type */
    public $type;

    public function __construct(
        string $id,
        float  $percentageDiscount,
        string $translationKey,
        string $type
    )
    {
        $this->id = $id;
        $this->percentageDiscount = $percentageDiscount;
        $this->translationKey = $translationKey;
        $this->type = $type;
    }
}

class CustomHandle
{
    /** @var int|null $count */
    public $count;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    public function __construct()
    {
        $this->count = null;
        $this->name = null;
        $this->price = null;
    }

    public static function fromRequest(?CustomHandleRequest $req): CustomHandle
    {
        $instance = new self();

        if ($req == null) {
            return $instance;
        }

        $instance->count = $req->count;
        $instance->name = $req->name;
        $instance->price = $req->price;

        return $instance;
    }

    public function toResponse(): CustomHandleResponse
    {
        return new CustomHandleResponse(
            $this->calculatePrice(),
            $this->count,
            $this->name,
            $this->price
        );
    }

    function calculatePrice(): float
    {
        return ($this->count ?? 0) * ($this->price ?? 0.0);
    }
}

class TechnicalSurcharge implements Product
{
    public static $SILICONE_ID = "technical-surcharges-silicone-wall-frame";
    public static $SILICONE_PRICE_MULTIPLIER = 0.7;

    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    /** @var bool|null $isCountDirty */
    public $isCountDirty;

    public function __construct(?string $id, ?int $count, ?bool $isCountDirty)
    {
        $this->id = $id;
        $this->count = $count;
        $this->isCountDirty = $isCountDirty;
    }

    public static function fromRequest(TechnicalSurchargeRequest $req): TechnicalSurcharge
    {
        return new self(
            $req->id,
            $req->count,
            $req->isCountDirty
        );
    }

    public function toResponse(?array $technicalSurchargeDb, int $doorsCount, ?string $currencyId): TechnicalSurchargeResponse
    {
        $basePrice = $this->getBasePriceFromDb($technicalSurchargeDb) ?? 0.0;
        $currencyPrice = currencyRateTransformation($currencyId, $basePrice, null);

        $effectiveCount = $this->getEffectiveCount($technicalSurchargeDb, $doorsCount);
        return new TechnicalSurchargeResponse(
            $this->id,
            $this->calculatePrice($technicalSurchargeDb, $effectiveCount, $currencyId),
            $currencyPrice,
            $effectiveCount,
            $this->isCountDirty,
            array_key_exists("header", $technicalSurchargeDb) ? $technicalSurchargeDb["header"] : null,
            array_key_exists("headerKey", $technicalSurchargeDb) ? $technicalSurchargeDb["headerKey"] : null,
            array_key_exists("hint", $technicalSurchargeDb) ? $technicalSurchargeDb["hint"] : null,
            array_key_exists("hintKey", $technicalSurchargeDb) ? $technicalSurchargeDb["hintKey"] : null,
            array_key_exists("imgSrc", $technicalSurchargeDb) ? $technicalSurchargeDb["imgSrc"] : null,
            array_key_exists("label", $technicalSurchargeDb) ? $technicalSurchargeDb["label"] : null,
            array_key_exists("labelKey", $technicalSurchargeDb) ? $technicalSurchargeDb["labelKey"] : null,
            array_key_exists("youtubeVideoCode", $technicalSurchargeDb) ? $technicalSurchargeDb["youtubeVideoCode"] : null,
            array_key_exists("videoSrc", $technicalSurchargeDb) ? $technicalSurchargeDb["videoSrc"] : null
        );
    }

    function getEffectiveCount(?array $technicalSurchargeDb, int $doorsCount): int
    {
        $setCountBasedOnDoorsCount = is_array($technicalSurchargeDb) && $technicalSurchargeDb["setCountBasedOnDoorsCount"];
        if (!$this->isCountDirty && $setCountBasedOnDoorsCount) {
            return $doorsCount;
        }

        return $this->count ?? 0;
    }

    function calculatePrice(?array $itemJson, int $effectiveCount, ?string $currencyId): float
    {
        $basePrice = $this->getBasePriceFromDb($itemJson) ?? 0.0;
        $currencyPrice = currencyRateTransformation($currencyId, $basePrice, null);

        $coefficient = $this->id == TechnicalSurcharge::$SILICONE_ID ? TechnicalSurcharge::$SILICONE_PRICE_MULTIPLIER : 1.0;
        return $effectiveCount * $currencyPrice * $coefficient;
    }

    public function getBasePriceFromDb(?array $dbRecord): ?float
    {
        $price = $dbRecord['price'] ?? null;

        if (!is_numeric($price)) {
            return null;
        }

        return (float)$price;
    }
}

class Rosette implements Product
{
    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    public function __construct(?string $id, ?int $count)
    {
        $this->id = $id;
        $this->count = $count;
    }

    public static function fromRequest(RosetteRequest $req): Rosette
    {
        return new self(
            $req->id,
            $req->count
        );
    }

    public function toResponse(array $rosetteDb, ?string $currencyId): RosetteResponse
    {
        $basePrice = $this->getBasePriceFromDb($rosetteDb) ?? 0.0;
        $currencyPrice = currencyRateTransformation($currencyId, $basePrice, null);

        return new RosetteResponse(
            $this->id,
            $this->calculatePrice($rosetteDb, $currencyId),
            $this->count,
            array_key_exists("header", $rosetteDb) ? $rosetteDb["header"] : null,
            array_key_exists("headerKey", $rosetteDb) ? $rosetteDb["headerKey"] : null,
            array_key_exists("hint", $rosetteDb) ? $rosetteDb["hint"] : null,
            array_key_exists("hintKey", $rosetteDb) ? $rosetteDb["hintKey"] : null,
            array_key_exists("imgSrc", $rosetteDb) ? $rosetteDb["imgSrc"] : null,
            array_key_exists("label", $rosetteDb) ? $rosetteDb["label"] : null,
            array_key_exists("labelKey", $rosetteDb) ? $rosetteDb["labelKey"] : null,
            $currencyPrice,
            array_key_exists("youtubeVideoCode", $rosetteDb) ? $rosetteDb["youtubeVideoCode"] : null,
            array_key_exists("videoSrc", $rosetteDb) ? $rosetteDb["videoSrc"] : null
        );
    }

    function calculatePrice(?array $rosetteDb, ?string $currencyId): float
    {
        $basePrice = $this->getBasePriceFromDb($rosetteDb) ?? 0.0;
        $currencyPrice = currencyRateTransformation($currencyId, $basePrice, null);
        return ($this->count ?? 0) * $currencyPrice;
    }

    public function getBasePriceFromDb(?array $dbRecord): ?float
    {
        $price = $dbRecord['price'] ?? null;

        if (!is_numeric($price)) {
            return null;
        }

        return (float)$price;
    }
}

class LineItem
{
    /** @var int|null $count */
    public $count;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    public function __construct(?int $count, ?string $name, ?float $price)
    {
        $this->count = $count;
        $this->name = $name;
        $this->price = $price;
    }

    public static function fromRequest(LineItemRequest $req): LineItem
    {
        return new self(
            $req->count,
            $req->name,
            $req->price
        );
    }

    public function toResponse(): LineItemResponse
    {
        return new LineItemResponse(
            $this->calculatePrice(),
            $this->count,
            $this->name,
            $this->price
        );
    }

    function calculatePrice(): float
    {
        return ($this->count ?? 0.0) * ($this->price ?? 0.0);
    }
}

class AestheticAccessory implements Product
{
    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    /** @var float|null $selectedPrice */
    public $selectedPrice;

    public function __construct(?string $id, ?int $count, ?float $selectedPrice)
    {
        $this->id = $id;
        $this->count = $count;
        $this->selectedPrice = $selectedPrice;
    }

    public static function fromRequest(AestheticAccessoryRequest $req): AestheticAccessory
    {
        return new self(
            $req->id,
            $req->count,
            $req->selectedPrice
        );
    }

    public function toResponse(array $aestheticAccessoryDb, ?string $currencyId): AestheticAccessoryResponse
    {
        $basePriceDb = $this->getBasePriceFromDb($aestheticAccessoryDb);
        $currencyPrice = $basePriceDb ? currencyRateTransformation($currencyId, $basePriceDb, null) : null;

        return new AestheticAccessoryResponse(
            $this->id,
            $this->calculatePrice($aestheticAccessoryDb, $currencyId),
            $currencyPrice,
            $this->count,
            array_key_exists("header", $aestheticAccessoryDb) ? $aestheticAccessoryDb["header"] : null,
            array_key_exists("headerKey", $aestheticAccessoryDb) ? $aestheticAccessoryDb["headerKey"] : null,
            array_key_exists("hint", $aestheticAccessoryDb) ? $aestheticAccessoryDb["hint"] : null,
            array_key_exists("hintKey", $aestheticAccessoryDb) ? $aestheticAccessoryDb["hintKey"] : null,
            array_key_exists("imgSrc", $aestheticAccessoryDb) ? $aestheticAccessoryDb["imgSrc"] : null,
            array_key_exists("label", $aestheticAccessoryDb) ? $aestheticAccessoryDb["label"] : null,
            array_key_exists("labelKey", $aestheticAccessoryDb) ? $aestheticAccessoryDb["labelKey"] : null,
            $this->selectedPrice,
            array_key_exists("youtubeVideoCode", $aestheticAccessoryDb) ? $aestheticAccessoryDb["youtubeVideoCode"] : null,
            array_key_exists("videoSrc", $aestheticAccessoryDb) ? $aestheticAccessoryDb["videoSrc"] : null
        );
    }

    //some items can have custom price from input
    function calculatePrice(array $aestheticAccessoryDb, ?string $currencyId): float
    {
        $basePriceDb = $this->getBasePriceFromDb($aestheticAccessoryDb);

        if ($basePriceDb != null) {
            return ($this->count ?? 0) * currencyRateTransformation($currencyId, $basePriceDb, null);
        }

        return ($this->count ?? 0) * ($this->selectedPrice ?? 0.0);
    }

    public function getBasePriceFromDb(?array $dbRecord): ?float
    {
        $price = $dbRecord['price'] ?? null;

        if (!is_numeric($price)) {
            return null;
        }

        return (float)$price;
    }
}

class SpecialSurcharge implements Product
{
    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    /** @var bool|null $isAssemblySelected */
    public $isAssemblySelected;

    /** @var bool|null $isAssemblySelectedDirty */
    public $isAssemblySelectedDirty;

    public function __construct(?string $id, ?int $count, ?bool $isAssemblySelected, ?bool $isAssemblySelectedDirty)
    {
        $this->id = $id;
        $this->count = $count;
        $this->isAssemblySelected = $isAssemblySelected;
        $this->isAssemblySelectedDirty = $isAssemblySelectedDirty;
    }

    public static function fromRequest(SpecialSurchargeRequest $req): SpecialSurcharge
    {
        return new self(
            $req->id,
            $req->count,
            SpecialSurcharge::getEffectiveIsAssemblySelected($req->count, $req->isAssemblySelected, $req->isAssemblySelectedDirty),
            $req->isAssemblySelectedDirty
        );
    }

    public function toResponse(?array $specialSurchargeDb, ?string $currencyId): SpecialSurchargeResponse
    {
        $basePriceDb = $this->getBasePriceFromDb($specialSurchargeDb) ?? 0.0;
        $currencyPrice = currencyRateTransformation($currencyId, $basePriceDb, null);

        return new SpecialSurchargeResponse(
            $this->id,
            array_key_exists("assemblyCosts", $specialSurchargeDb) ? currencyRateTransformation($currencyId, $specialSurchargeDb["assemblyCosts"], null) : null,
            $this->calculatePrice($specialSurchargeDb, $currencyId),
            $currencyPrice,
            $this->count,
            array_key_exists("header", $specialSurchargeDb) ? $specialSurchargeDb["header"] : null,
            array_key_exists("headerKey", $specialSurchargeDb) ? $specialSurchargeDb["headerKey"] : null,
            array_key_exists("hint", $specialSurchargeDb) ? $specialSurchargeDb["hint"] : null,
            array_key_exists("hintKey", $specialSurchargeDb) ? $specialSurchargeDb["hintKey"] : null,
            array_key_exists("imgSrc", $specialSurchargeDb) ? $specialSurchargeDb["imgSrc"] : null,
            SpecialSurcharge::getEffectiveIsAssemblySelected($this->count, $this->isAssemblySelected, $this->isAssemblySelectedDirty),
            $this->isAssemblySelectedDirty,
            array_key_exists("label", $specialSurchargeDb) ? $specialSurchargeDb["label"] : null,
            array_key_exists("labelKey", $specialSurchargeDb) ? $specialSurchargeDb["labelKey"] : null,
            array_key_exists("labelAssembly", $specialSurchargeDb) ? $specialSurchargeDb["labelAssembly"] : null,
            array_key_exists("labelAssemblyKey", $specialSurchargeDb) ? $specialSurchargeDb["labelAssemblyKey"] : null,
            array_key_exists("youtubeVideoCode", $specialSurchargeDb) ? $specialSurchargeDb["youtubeVideoCode"] : null,
            array_key_exists("videoSrc", $specialSurchargeDb) ? $specialSurchargeDb["videoSrc"] : null
        );
    }

    function calculatePrice(array $specialSurchargeDb, ?string $currencyId): float
    {
        $assemblyCosts = $specialSurchargeDb["assemblyCosts"] ?? 0.0;
        $assemblyPrice = $this->isAssemblySelected ? $assemblyCosts : 0.0;
        $price = $this->getBasePriceFromDb($specialSurchargeDb) ?? 0.0;
        return ($this->count ?? 0) * currencyRateTransformation($currencyId, $price + $assemblyPrice, null);
    }

    static function getEffectiveIsAssemblySelected(?int $count, ?bool $isAssemblySelected, ?bool $isAssemblySelectedDirty): bool
    {
        if ($count == null) {
            return false;
        }

        return $isAssemblySelectedDirty
            ? $isAssemblySelected
            : true;
    }

    public function getBasePriceFromDb(?array $dbRecord): ?float
    {
        $price = $dbRecord['price'] ?? null;

        if (!is_numeric($price)) {
            return null;
        }

        return (float)$price;
    }
}

class Door implements Product
{
    /** @var string|null $category */
    public $category;

    /** @var string|null $type */
    public $type;

    /** @var string|null $material */
    public $material;

    /** @var string|null $width */
    public $width;

    /** @var int|null $count */
    public $count;

    /** @var float|null $price */
    public $price;

    /** @var string|null $info */
    public $info;

    /** @var boolean|null $frame */
    public $frame;

    /** @var boolean|null $assembly */
    public $assembly;

    /** @var boolean|null $isDtdSelected */
    public $isDtdSelected;

    function __construct(?string $c, ?string $t, ?string $m, ?string $w, ?int $cn, ?string $nfo, ?bool $frame,
                         ?bool   $assembly, ?bool $isDtdSelected)
    {
        $this->category = $c;
        $this->isDtdSelected = $isDtdSelected;
        $this->type = $t;
        $this->material = $m;
        $this->width = $w ?? "";
        $this->count = $cn;
        $this->info = $nfo;
        $this->price = $this->getBasePriceFromDb(null);
        $this->frame = $frame;
        $this->assembly = $assembly;
    }

    public static function fromRequest(DoorRequest $req): Door
    {
        return new self(
            $req->category,
            $req->type,
            $req->material,
            $req->width ?? "",
            1,
            null,
            $req->isDoorFrameEnabled,
            null,
            $req->isDtdSelected
        );
    }

    public function toResponse(?string $currency): DoorResponse
    {
        return new DoorResponse(
            $this->calculatePrice($currency),
            $this->category,
            $this->frame,
            $this->isDtdAvailable(),
            $this->isDtdSelected,
            $this->material,
            $this->type,
            $this->width
        );
    }

    function calculatePrice(string $currencyId): float
    {
        $price = $this->getBasePriceFromDb(null);

        if ($this->frame) {
            $price = $price + ($this->type == "v1" ? ParametersJsonDataManipulation::getAll()["framePriceOffer"] : ParametersJsonDataManipulation::getAll()["framePrice"]);//extra fee for frame
        }

        $price = $price + DoorsWidthEnum::getFee($this->width);//extra fee for width
        $price = $price + ($this->isDtdSelected ? ParametersJsonDataManipulation::getAll()["dtdSurcharge"] : 0);//extra fee for DTD
        return ($this->count ?? 0) * currencyRateTransformation($currencyId, $price, null);
    }

    function isDtdAvailable(): bool
    {
        return $this->type == "v1" || in_array($this->category, array("petra", "vanesa"));
    }

    public function getBasePriceFromDb(?array $dbRecord): ?float
    {
        try {
            $list = DoorCategoriesJsonDataManipulation::getAll();
            $item = array_filter($list, function ($r) {
                return $r["categoryId"] == $this->category;
            });
            $item = reset($item) ?? null;

            $price = $item["doorTypes"][$this->type]["price"] ?? null;

            if (!is_numeric($price)) {
                return null;
            }

            return (float)$price;
        } catch (Exception $e) {
            return null;
        }
    }
}

class SelectedDoorLineItem
{
    /** @var bool|null $isDoorFrameEnabled */
    public $isDoorFrameEnabled;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    /** @var string|null $width */
    public $width;

    function __construct(?bool $isDoorFrameEnabled, ?string $name, ?float $price, ?string $width)
    {
        $this->isDoorFrameEnabled = $isDoorFrameEnabled;
        $this->name = $name;
        $this->price = $price;
        $this->width = $width;
    }

    public static function fromRequest(SelectedDoorLineItemRequest $req): SelectedDoorLineItem
    {
        return new self(
            $req->isDoorFrameEnabled,
            $req->name,
            $req->price,
            $req->width
        );
    }

    public function toResponse(?string $currency): SelectedDoorLineItemResponse
    {
        return new SelectedDoorLineItemResponse(
            $this->calculatePrice(),
            $this->isDoorFrameEnabled,
            $this->name,
            $this->price,
            $this->width
        );
    }

    function calculatePrice(): float
    {
        $effectivePrice = $this->price ?? 0.0;
        if ($this->isDoorFrameEnabled) {
            $effectivePrice = $effectivePrice + ParametersJsonDataManipulation::getAll()["framePrice"];//extra fee for frame
        }

        //extra fee for width
        return $effectivePrice + DoorsWidthEnum::getFee($this->width);
    }
}

class PriceOffer
{
    /** @var Address $address */
    public $address;
    /** @var int|null $assemblyHandlesRosettesCount */
    public $assemblyHandlesRosettesCount;
    /** @var int|null $assemblyDoorsCount */
    public $assemblyDoorsCount;
    /** @var Door[] $doors */
    public $doors;
    /** @var Contact $contact */
    public $contact;
    /** @var string|null $currency */
    public $currency;
    /** @var CustomHandle $customHandle */
    public $customHandle;
    /** @var Handle|null $handle */
    public $handle;
    /** @var bool|null $isAssemblyDoorsCountDirty */
    public $isAssemblyDoorsCountDirty;
    /** @var bool|null $isAssemblyHandlesRosettesCountDirty */
    public $isAssemblyHandlesRosettesCountDirty;
    /** @var string|null $note */
    public $note;
    /** @var TechnicalSurcharge[] $technicalSurcharges */
    public $technicalSurcharges;
    /** @var LineItem[] $technicalSurchargesLineItems */
    public $technicalSurchargesLineItems;
    /** @var SelectedDoorLineItem[] $selectedDoorsLineItems */
    public $selectedDoorsLineItems;
    /** @var Rosette[] $selectedRosettes */
    public $selectedRosettes;
    /** @var LineItem[] $rosettesLineItems */
    public $rosettesLineItems;
    /** @var AestheticAccessory[] $aestheticAccessories */
    public $aestheticAccessories;
    /** @var LineItem[] $aestheticAccessoriesLineItems */
    public $aestheticAccessoriesLineItems;
    /** @var SpecialSurcharge[] $specialSurcharges */
    public $specialSurcharges;
    /** @var LineItem[] $specialSurchargesLineItems */
    public $specialSurchargesLineItems;

    function __construct(?string $lang)
    {
        $this->address = new Address($lang);
        $this->assemblyDoorsCount = null;
        $this->assemblyHandlesRosettesCount = null;
        $this->contact = new Contact();
        $this->currency = CurrencyEnum::EUR;
        $this->doors = array();
        $this->customHandle = new CustomHandle();
        $this->handle = null;
        $this->isAssemblyDoorsCountDirty = null;
        $this->isAssemblyHandlesRosettesCountDirty = null;
        $this->note = null;
        $this->selectedDoorsLineItems = array();
        $this->selectedRosettes = array();
        $this->rosettesLineItems = array();
        $this->aestheticAccessories = array();
        $this->aestheticAccessoriesLineItems = array();
        $this->specialSurcharges = array();
        $this->specialSurchargesLineItems = array();
        $this->technicalSurcharges = array_map(function (array $db) {
            return new TechnicalSurcharge(
                array_key_exists("id", $db) ? $db["id"] : null,
                0,
                null
            );
        }, TechnicalSurchargesJsonDataManipulation::getAll());
        $this->technicalSurchargesLineItems = array();
    }

    public static function fromSession(PriceOffer $session): PriceOffer
    {
        $instance = new self(null);
        $instance->address = $session->address ?? new Address(null);
        $instance->assemblyHandlesRosettesCount = $session->assemblyHandlesRosettesCount ?? null;
        $instance->assemblyDoorsCount = $session->assemblyDoorsCount ?? null;
        $instance->contact = $session->contact ?? new Contact();
        $instance->currency = $session->currency ?? CurrencyEnum::EUR;
        $instance->doors = $session->doors ?? array();
        $instance->customHandle = $session->customHandle ?? new CustomHandle();
        $instance->handle = $session->handle ?? null;
        $instance->isAssemblyDoorsCountDirty = $session->isAssemblyDoorsCountDirty ?? null;
        $instance->isAssemblyHandlesRosettesCountDirty = $session->isAssemblyHandlesRosettesCountDirty ?? null;
        $instance->note = $session->note ?? null;
        $instance->technicalSurcharges = $session->technicalSurcharges ?? array();
        $instance->technicalSurchargesLineItems = $session->technicalSurchargesLineItems ?? array();
        $instance->selectedDoorsLineItems = $session->selectedDoorsLineItems ?? array();
        $instance->selectedRosettes = $session->selectedRosettes ?? array();
        $instance->rosettesLineItems = $session->rosettesLineItems ?? array();
        $instance->aestheticAccessories = $session->aestheticAccessories ?? array();
        $instance->aestheticAccessoriesLineItems = $session->aestheticAccessoriesLineItems ?? array();
        $instance->specialSurcharges = $session->specialSurcharges ?? array();
        $instance->specialSurchargesLineItems = $session->specialSurchargesLineItems ?? array();

        return $instance;
    }

    public static function fromRequest(PriceOfferRequest $req, ?string $lang): PriceOffer
    {
        $instance = new self($lang);
        $instance->address = Address::fromRequest($req->address, $lang);
        $instance->assemblyDoorsCount = $req->assemblyDoorsCount;
        $instance->assemblyHandlesRosettesCount = $req->assemblyHandlesRosettesCount;
        $instance->contact = Contact::fromRequest($req->contact);
        $instance->currency = $req->currency ?? CurrencyEnum::EUR;

        $instance->doors = is_array($req->doors) ? array_map(function (DoorRequest $value): Door {
            return Door::fromRequest($value);
        }, $req->doors) : array();

        $instance->customHandle = CustomHandle::fromRequest($req->customHandle);
        $instance->handle = $req->handle ? Handle::fromRequest($req->handle) : null;
        $instance->isAssemblyDoorsCountDirty = $req->isAssemblyDoorsCountDirty;
        $instance->isAssemblyHandlesRosettesCountDirty = $req->isAssemblyHandlesRosettesCountDirty;
        $instance->note = $req->note;

        $instance->technicalSurcharges = is_array($req->technicalSurcharges) ? array_map(function (TechnicalSurchargeRequest $value): TechnicalSurcharge {
            return TechnicalSurcharge::fromRequest($value);
        }, $req->technicalSurcharges) : array();

        $instance->technicalSurchargesLineItems = is_array($req->technicalSurchargesLineItems) ? array_map(function (LineItemRequest $value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->technicalSurchargesLineItems) : array();

        $instance->selectedRosettes = is_array($req->rosettes) ? array_map(function (RosetteRequest $value): Rosette {
            return Rosette::fromRequest($value);
        }, $req->rosettes) : array();

        $instance->rosettesLineItems = is_array($req->rosettesLineItems) ? array_map(function (LineItemRequest $value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->rosettesLineItems) : array();

        $instance->selectedDoorsLineItems = is_array($req->selectedDoorsLineItems) ? array_map(function (SelectedDoorLineItemRequest $value): SelectedDoorLineItem {
            return SelectedDoorLineItem::fromRequest($value);
        }, $req->selectedDoorsLineItems) : array();

        $instance->aestheticAccessories = is_array($req->aestheticAccessories) ? array_map(function (AestheticAccessoryRequest $value): AestheticAccessory {
            return AestheticAccessory::fromRequest($value);
        }, $req->aestheticAccessories) : array();

        $instance->aestheticAccessoriesLineItems = is_array($req->aestheticAccessoriesLineItems) ? array_map(function (LineItemRequest $value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->aestheticAccessoriesLineItems) : array();

        $instance->specialSurcharges = is_array($req->specialSurcharges) ? array_map(function (SpecialSurchargeRequest $value): SpecialSurcharge {
            return SpecialSurcharge::fromRequest($value);
        }, $req->specialSurcharges) : array();

        $instance->specialSurchargesLineItems = is_array($req->specialSurchargesLineItems) ? array_map(function (LineItemRequest $value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->specialSurchargesLineItems) : array();

        return $instance;
    }

    public function fromPostConfigurator(ConfiguratorPostRequest $request): PriceOffer
    {
        $door = new Door(
            $request->category,
            $request->type,
            $request->material,
            null,
            1,
            null,
            true,
            null,
            null
        );

        $this->doors[] = $door;
        $handleCount = $this->handle ? $this->handle->getEffectiveCount($this->getDoorsCount()) : 1;
        $this->handle = $request->handleId ? Handle::fromConfiguratorPost(
            $handleCount,
            $this->handle ? $this->handle->isCountDirty : false,
            $request->handleId
        ) : null;

        return $this;
    }

    public function toResponse(string $currency, array $doorsCampaigns): PriceOfferResponse
    {
        $this->currency = $currency;
        $doorsCount = $this->getDoorsCount();
        $assemblyDoorsEffectiveCount = $this->getEffectiveCountIsAssemblyDoorsCount($doorsCount);
        $assemblyHandlesRosettesEffectiveCount = $this->getEffectiveCountIsAssemblyHandlesRosettesCount($doorsCount);

        $assemblyDoorsCalculatedPrice = $this->calculateDoorsAssemblyCosts($assemblyDoorsEffectiveCount);
        $calculatedAestheticAccessoriesPrice = $this->calculateAestheticAccessoriesPrice();
        $calculatedDeliveryCosts = $this->calculateDeliveryCosts();

        $calculatedDoorsPrice = $this->calculateDoorsPrice($doorsCampaigns);
        $calculatedDoorsPriceNoCampaign = $this->calculateDoorsPrice(array());

        $calculatedHandlesAndRosettesPrice = $this->calculateHandlesAndRosettesPrice();
        $calculatedSpecialSurchargesPrice = $this->calculateSpecialSurchargesPrice();
        $technicalSurchargesCalculatedPrice = $this->calculateTechnicalSurchargesPrice($doorsCount);
        $price = $this->calculatePrice(
            $assemblyDoorsCalculatedPrice,
            $calculatedAestheticAccessoriesPrice,
            $calculatedDeliveryCosts,
            $calculatedDoorsPrice,
            $calculatedHandlesAndRosettesPrice,
            $calculatedSpecialSurchargesPrice,
            $technicalSurchargesCalculatedPrice
        );

        return new PriceOfferResponse(
            $this->address ? $this->address->toResponse() : AddressResponse::empty(),
            $assemblyDoorsCalculatedPrice,
            currencyRateTransformation($this->currency, ParametersJsonDataManipulation::getAll()["assemblyDoorsCost"] ?? 0.0, null),
            currencyRateTransformation($this->currency, ParametersJsonDataManipulation::getAll()["assemblyDoorsCostMin"] ?? 0.0, null),
            $assemblyDoorsEffectiveCount,
            $assemblyHandlesRosettesEffectiveCount,
            $this->calculateAssemblyPriceHandlesRosettes($assemblyHandlesRosettesEffectiveCount),
            $price,
            $this->calculatePriceVat($price),
            getCalculatedCampaignDetails($calculatedDoorsPriceNoCampaign, $doorsCampaigns),
            $this->contact ? $this->contact->toResponse() : ContactResponse::empty(),
            $this->currency,
            $calculatedDeliveryCosts,
            currencyRateTransformation($this->currency, ParametersJsonDataManipulation::getAll()["deliveryPricePerKm"], null),
            array_map(function (Door $door): DoorResponse {
                return $door->toResponse($this->currency);
            }, $this->doors),
            $this->customHandle ? $this->customHandle->toResponse() : CustomHandleResponse::empty(),
            ($this->handle && HandlesJsonDataManipulation::findByIdOrNull($this->handle->id)) ?
                $this->handle->toResponse(HandlesJsonDataManipulation::findByIdOrNull($this->handle->id), $doorsCount, $this->currency) :
                null,
            $this->isAssemblyDoorsCountDirty,
            $this->isAssemblyHandlesRosettesCountDirty,
            $this->note,
            mapTechnicalSurchargesToResponse($this->technicalSurcharges, $doorsCount, $this->currency),
            array_map(function (LineItem $it): LineItemResponse {
                return $it->toResponse();
            }, $this->technicalSurchargesLineItems),
            mapRosettesToResponse($this->selectedRosettes, $this->currency),
            array_map(function (LineItem $it): LineItemResponse {
                return $it->toResponse();
            }, $this->rosettesLineItems),
            new CalculatedPriceResponse(
                $calculatedDoorsPriceNoCampaign,//doors
                $calculatedHandlesAndRosettesPrice,//handlesAndRosettes
                $calculatedDeliveryCosts,//delivery
                $assemblyDoorsCalculatedPrice,//assemblyDoors
                $calculatedAestheticAccessoriesPrice,//aestheticAccessories
                $technicalSurchargesCalculatedPrice,//technicalSurcharges
                $calculatedSpecialSurchargesPrice//specialSurcharges
            ),
            array_map(function (SelectedDoorLineItem $it): SelectedDoorLineItemResponse {
                return $it->toResponse($this->currency);
            }, $this->selectedDoorsLineItems),
            mapAestheticAccessoriesToResponse($this->aestheticAccessories, $this->currency),
            array_map(function (LineItem $it): LineItemResponse {
                return $it->toResponse();
            }, $this->aestheticAccessoriesLineItems),
            mapSpecialSurchargesToResponse($this->specialSurcharges, $this->currency),
            array_map(function (LineItem $it): LineItemResponse {
                return $it->toResponse();
            }, $this->specialSurchargesLineItems),
        );
    }

    function getEffectiveCountIsAssemblyDoorsCount(?int $doorsCount): int
    {
        if (!$this->isAssemblyDoorsCountDirty) {
            return $doorsCount;
        }

        return $this->assemblyDoorsCount ?? 0;
    }

    function getEffectiveCountIsAssemblyHandlesRosettesCount(?int $doorsCount): int
    {
        if (!$this->isAssemblyHandlesRosettesCountDirty) {
            return $doorsCount;
        }

        return $this->assemblyHandlesRosettesCount ?? 0;
    }

    function getDoorsCount(): float
    {
        $count = 0;
        if (!empty($this->doors)) {
            foreach ($this->doors as $door) {
                $count += $door->count;
            }
        }
        return $count;
    }

    function calculateDoorsAssemblyCosts(?int $assemblyDoorsCount): float
    {
        if ($assemblyDoorsCount != null && $assemblyDoorsCount > 0) {
            $result = max(
                $assemblyDoorsCount * ParametersJsonDataManipulation::getAll()["assemblyDoorsCost"],
                ParametersJsonDataManipulation::getAll()["assemblyDoorsCostMin"]
            );
            return currencyRateTransformation($this->currency, $result, null);
        }

        return 0.0;
    }

    function calculateDeliveryCosts(): float
    {
        if ($this->address) {
            $config = DistrictsJsonDataManipulation::findByIdAndCountryOrNull($this->address->district, $this->address->deliveryCountry);
            return $config ?
                currencyRateTransformation(
                    $this->currency,
                    2 * $config["distance"] * ParametersJsonDataManipulation::getAll()["deliveryPricePerKm"], null
                ) : 0.0;
        }

        return 0.0;
    }

    /**
     * @param Campaign[] $campaigns
     * @return float
     */
    function calculateDoorsPrice(array $campaigns): float
    {
        $doorsPrice = array_sum(array_map(function ($door): float {
            return $door->calculatePrice($this->currency);
        }, $this->doors ?? array()));

        $selectedDoorsLineItemsPrice = array_sum(array_map(function ($it): float {
            return $it->calculatePrice();
        }, $this->selectedDoorsLineItems ?? array()));

        return getCampaignPrice($doorsPrice + $selectedDoorsLineItemsPrice, $campaigns);
    }

    function calculateHandlesAndRosettesPrice(): float
    {
        $customHandlePrice = $this->customHandle->calculatePrice();
        $handlePrice = $this->handle ?
            $this->handle->calculatePrice($this->handle->getEffectiveCount($this->getDoorsCount()), $this->currency) :
            0.0;
        $rosettesPrice = array_sum(array_map(function ($rosette): float {
            return $rosette->calculatePrice(RosettesJsonDataManipulation::findByIdOrNull($rosette->id), $this->currency);
        }, $this->selectedRosettes));
        $rosettesLineItemsPrice = array_sum(array_map(function ($it): float {
            return $it->calculatePrice();
        }, $this->rosettesLineItems));

        $doorsCount = $this->getDoorsCount();
        $assemblyHandlesRosettesEffectiveCount = $this->getEffectiveCountIsAssemblyHandlesRosettesCount($doorsCount);
        $assemblyPriceHandlesRosettes = $this->calculateAssemblyPriceHandlesRosettes($assemblyHandlesRosettesEffectiveCount);

        return $customHandlePrice + $handlePrice + $rosettesPrice + $rosettesLineItemsPrice +
            $assemblyPriceHandlesRosettes;
    }

    function calculateTechnicalSurchargesPrice(int $doorsCount): float
    {
        $chargesTotal = array_sum(array_map(
            function (TechnicalSurcharge $charge) use ($doorsCount): float {
                $technicalSurchargeDb = TechnicalSurchargesJsonDataManipulation::findByIdOrNull($charge->id);
                $effectiveCount = $charge->getEffectiveCount($technicalSurchargeDb, $doorsCount);
                return $charge->calculatePrice(
                    $technicalSurchargeDb,
                    $effectiveCount,
                    $this->currency
                );
            }, $this->technicalSurcharges ?? array()
        ));

        $lineItemsTotal = array_sum(array_map(
            function ($item): float {
                return $item->calculatePrice();
            }, $this->technicalSurchargesLineItems ?? array()
        ));

        return $chargesTotal + $lineItemsTotal;
    }

    function calculateAestheticAccessoriesPrice(): float
    {
        $aestheticAccessoriesTotal = array_sum(array_map(
            function ($aestheticAccessory): float {
                return $aestheticAccessory->calculatePrice(
                    AestheticAccessoriesJsonDataManipulation::findByIdOrNull($aestheticAccessory->id),
                    $this->currency
                );
            },
            $this->aestheticAccessories
        ));

        $lineItemsTotal = array_sum(array_map(
            function ($item): float {
                return $item->calculatePrice();
            },
            $this->aestheticAccessoriesLineItems
        ));

        return $aestheticAccessoriesTotal + $lineItemsTotal;
    }

    function calculateSpecialSurchargesPrice(): float
    {
        $specialSurchargesTotal = array_sum(array_map(
            function ($specialSurcharge): float {
                return $specialSurcharge->calculatePrice(
                    SpecialSurchargesJsonDataManipulation::findByIdOrNull($specialSurcharge->id),
                    $this->currency
                );
            },
            $this->specialSurcharges
        ));

        $lineItemsTotal = array_sum(array_map(
            function ($item): float {
                return $item->calculatePrice();
            },
            $this->specialSurchargesLineItems
        ));

        return $specialSurchargesTotal + $lineItemsTotal;
    }

    function calculatePrice(
        float $assemblyDoorsCalculatedPrice,
        float $calculatedAestheticAccessoriesPrice,
        float $calculatedDeliveryCosts,
        float $calculatedDoorsPrice,
        float $calculatedHandlesAndRosettesPrice,
        float $calculatedSpecialSurchargesPrice,
        float $technicalSurchargesCalculatedPrice
    ): float
    {
        return $calculatedDoorsPrice +
            $calculatedHandlesAndRosettesPrice +
            $assemblyDoorsCalculatedPrice +
            $calculatedDeliveryCosts +
            $calculatedAestheticAccessoriesPrice +
            $technicalSurchargesCalculatedPrice +
            $calculatedSpecialSurchargesPrice;
    }

    function calculatePriceVat(float $priceNoVat): float
    {
        return $priceNoVat * ParametersJsonDataManipulation::getAll()["vat"];
    }

    function calculateAssemblyPriceHandlesRosettes(int $effectiveCount): float
    {
        return $effectiveCount * currencyRateTransformation(
                $this->currency,
                ParametersJsonDataManipulation::getAll()["assemblyPriceHandlesRosettes"],
                null
            );
    }
}

function mapTechnicalSurchargesToResponse(array $technicalSurcharges, int $doorsCount, ?string $currency): array
{
    return array_map(function ($itemJson) use ($currency, $doorsCount, $technicalSurcharges): TechnicalSurchargeResponse {
        $id = $itemJson["id"];
        $technicalSurcharge = array_filter($technicalSurcharges, function ($r) use ($id) {
            return $r->id === $id;
        });

        /** @var TechnicalSurcharge|false $technicalSurcharge */
        $technicalSurcharge = reset($technicalSurcharge);
        return $technicalSurcharge
            ? $technicalSurcharge->toResponse($itemJson, $doorsCount, $currency)
            : (new TechnicalSurcharge($id, null, null))->toResponse($itemJson, $doorsCount, $currency);
    }, TechnicalSurchargesJsonDataManipulation::getAll());
}

function mapRosettesToResponse(array $selectedRosettes, ?string $currency): array
{
    return array_map(function ($rosetteDb) use ($currency, $selectedRosettes): RosetteResponse {
        $id = $rosetteDb["id"];

        $selectedRosette = array_filter($selectedRosettes, function ($r) use ($id) {
            return $r->id === $id;
        });
        /** @var Rosette|false $selectedRosette */
        $selectedRosette = reset($selectedRosette);
        return $selectedRosette
            ? $selectedRosette->toResponse($rosetteDb, $currency)
            : (new Rosette($id, null))->toResponse($rosetteDb, $currency);
    }, RosettesJsonDataManipulation::getAll());
}

function mapAestheticAccessoriesToResponse(array $aestheticAccessories, ?string $currencyId): array
{
    return array_map(function ($aestheticAccessoryDb) use ($aestheticAccessories, $currencyId): AestheticAccessoryResponse {
        $id = $aestheticAccessoryDb["id"];

        $aestheticAccessory = array_filter($aestheticAccessories, function ($r) use ($id) {
            return $r->id === $id;
        });
        /** @var AestheticAccessory|false $aestheticAccessory */
        $aestheticAccessory = reset($aestheticAccessory);
        return $aestheticAccessory
            ? $aestheticAccessory->toResponse($aestheticAccessoryDb, $currencyId)
            : (new AestheticAccessory($id, 0, 0))->toResponse($aestheticAccessoryDb, $currencyId);
    }, AestheticAccessoriesJsonDataManipulation::getAll());
}

function mapSpecialSurchargesToResponse(array $selectedSpecialSurcharges, ?string $currencyId): array
{
    return array_map(function ($specialSurchargeDb) use ($currencyId, $selectedSpecialSurcharges): SpecialSurchargeResponse {
        $id = $specialSurchargeDb["id"];

        $specialSurcharge = array_filter($selectedSpecialSurcharges, function ($r) use ($id) {
            return $r->id === $id;
        });
        /** @var SpecialSurcharge|false $specialSurcharge */
        $specialSurcharge = reset($specialSurcharge);
        return $specialSurcharge
            ? $specialSurcharge->toResponse($specialSurchargeDb, $currencyId)
            : (new SpecialSurcharge($id, 0, false, false))->toResponse($specialSurchargeDb, $currencyId);
    }, SpecialSurchargesJsonDataManipulation::getAll());
}