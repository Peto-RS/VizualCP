<?php

require_once __DIR__ . '/../../common/facade/json-data-manipulation-facade.php';
require_once __DIR__ . '/../req/api-price-offer-request-objects.php';
require_once __DIR__ . '/../res/api-configurator-response-objects.php';
require_once __DIR__ . '/../res/api-price-offer-response-objects.php';
require_once __DIR__ . '/../../../enums/CurrencyEnum.php';
require_once __DIR__ . '/../../../enums/DoorsWidthEnum.php';
require_once __DIR__ . '/../../common/model/model-common.php';

class SecurityDoorsDoor implements Product
{
    /** @var string|null $type */
    public $type;

    /** @var string|null $frameColor */
    public $frameColor;

    /** @var string|null $material */
    public $material;

    /** @var int|null $count */
    public $count;

    /** @var float|null $price */
    public $price;

    /** @var string|null $width */
    public $width;

    function __construct(
        ?string $t,
        ?string $frameColor,
        ?string $m,
        ?string $w,
        ?int    $cn
    )
    {
        $this->type = $t;
        $this->frameColor = $frameColor;
        $this->material = $m;
        $this->count = $cn;
        $this->width = $w;
        $this->price = $this->getBasePriceFromDb(null);
    }

    public static function fromRequest(SecurityDoorsDoorRequest $req): SecurityDoorsDoor
    {
        return new self(
            $req->type,
            $req->frameColor,
            $req->material,
            $req->width ?? "",
            $req->count ?? 1
        );
    }

    public function toResponse(string $currency): SecurityDoorsDoorResponse
    {
        return new SecurityDoorsDoorResponse(
            $this->calculatePrice($currency),
            $this->frameColor,
            $this->material,
            $this->type,
            $this->width
        );
    }

    function calculatePrice(string $currencyId): float
    {
        $price = $this->getBasePriceFromDb(null);
        $price = $price + DoorsWidthEnum::getFee($this->width);//extra fee for width
        return ($this->count ?? 0) * currencyRateTransformation($currencyId, $price, null);
    }

    public function getBasePriceFromDb(?array $dbRecord): ?float
    {
        try {
            $list = SecurityDoorCategoriesJsonDataManipulation::getAll();
            $item = array_filter($list, function ($r) {
                return $r["categoryId"] == "security";
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

class SecurityDoorsPriceOffer
{
    /** @var Address $address */
    public $address;
    /** @var int|null $assemblyDoorsCount */
    public $assemblyDoorsCount;
    /** @var Door[] $doors */
    public $doors;
    /** @var Contact $contact */
    public $contact;
    /** @var string|null $currency */
    public $currency;
    /** @var Handle|null $handle */
    public $handle;
    /** @var bool|null $isAssemblyDoorsCountDirty */
    public $isAssemblyDoorsCountDirty;
    /** @var string|null $note */
    public $note;

    function __construct(?string $lang)
    {
        $this->address = new Address($lang);
        $this->assemblyDoorsCount = null;
        $this->contact = new Contact();
        $this->currency = CurrencyEnum::EUR;
        $this->doors = array();
        $this->handle = null;
        $this->isAssemblyDoorsCountDirty = null;
        $this->note = null;
    }

    public static function fromSession(SecurityDoorsPriceOffer $session): SecurityDoorsPriceOffer
    {
        $instance = new self(null);
        $instance->address = $session->address ?? new Address(null);
        $instance->assemblyDoorsCount = $session->assemblyDoorsCount ?? null;
        $instance->contact = $session->contact ?? new Contact();
        $instance->currency = $session->currency ?? CurrencyEnum::EUR;
        $instance->doors = $session->doors ?? array();
        $instance->handle = $session->handle ?? null;
        $instance->isAssemblyDoorsCountDirty = $session->isAssemblyDoorsCountDirty ?? null;
        $instance->note = $session->note ?? null;

        return $instance;
    }

    public static function fromRequest(SecurityDoorsPriceOfferRequest $req, ?string $lang): SecurityDoorsPriceOffer
    {
        $instance = new self($lang);
        $instance->address = Address::fromRequest($req->address, $lang);
        $instance->assemblyDoorsCount = $req->assemblyDoorsCount;
        $instance->contact = Contact::fromRequest($req->contact);
        $instance->currency = $req->currency ?? CurrencyEnum::EUR;

        $instance->doors = is_array($req->doors) ? array_map(function (SecurityDoorsDoorRequest $value): SecurityDoorsDoor {
            return SecurityDoorsDoor::fromRequest($value);
        }, $req->doors) : array();

        $instance->handle = $req->handle ? Handle::fromRequest($req->handle) : null;
        $instance->isAssemblyDoorsCountDirty = $req->isAssemblyDoorsCountDirty;
        $instance->note = $req->note;

        return $instance;
    }

    public function fromPostConfigurator(SecurityDoorsConfiguratorPostRequest $request): SecurityDoorsPriceOffer
    {
        $door = new SecurityDoorsDoor(
            $request->type,
            $request->frameColor,
            $request->material,
            $request->width,
            1
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

    public function toResponse(string $currency): SecurityDoorsPriceOfferResponse
    {
        $this->currency = $currency;
        $doorsCount = $this->getDoorsCount();
        $assemblyDoorsEffectiveCount = $this->getEffectiveCountIsAssemblyDoorsCount($doorsCount);

        $assemblyDoorsCalculatedPrice = $this->calculateDoorsAssemblyCosts($assemblyDoorsEffectiveCount);
        $calculatedDeliveryCosts = $this->calculateDeliveryCosts();

        $calculatedDoorsPrice = $this->calculateDoorsPrice();
        $calculatedDoorsPriceNoCampaign = $this->calculateDoorsPrice();

        $calculatedHandlesAndRosettesPrice = 0.0;
        $price = $this->calculatePrice(
            $assemblyDoorsCalculatedPrice,
            $calculatedDeliveryCosts,
            $calculatedDoorsPrice,
            $calculatedHandlesAndRosettesPrice
        );

        return new SecurityDoorsPriceOfferResponse(
            $this->address ? $this->address->toResponse() : AddressResponse::empty(),
            $assemblyDoorsCalculatedPrice,
            currencyRateTransformation($this->currency, SecurityDoorsParametersJsonDataManipulation::getAll()["assemblyDoorsCost"] ?? 0.0, null),
            $assemblyDoorsEffectiveCount,
            $price,
            $this->calculatePriceVat($price),
            $this->contact ? $this->contact->toResponse() : ContactResponse::empty(),
            $this->currency,
            $calculatedDeliveryCosts,
            currencyRateTransformation($this->currency, SecurityDoorsParametersJsonDataManipulation::getAll()["deliveryPricePerKm"], null),
            array_map(function (SecurityDoorsDoor $door): SecurityDoorsDoorResponse {
                return $door->toResponse($this->currency);
            }, $this->doors),
            ($this->handle && HandlesJsonDataManipulation::findByIdOrNull($this->handle->id)) ?
                $this->handle->toResponse(HandlesJsonDataManipulation::findByIdOrNull($this->handle->id), $doorsCount, $this->currency) :
                null,
            $this->isAssemblyDoorsCountDirty,
            $this->note,
            new SecurityDoorsCalculatedPriceResponse(
                $calculatedDoorsPriceNoCampaign,//doors
                $calculatedHandlesAndRosettesPrice,//handlesAndRosettes
                $calculatedDeliveryCosts,//delivery
                $assemblyDoorsCalculatedPrice//assemblyDoors
            )
        );
    }

    function getEffectiveCountIsAssemblyDoorsCount(?int $doorsCount): int
    {
        if (!$this->isAssemblyDoorsCountDirty) {
            return $doorsCount;
        }

        return $this->assemblyDoorsCount ?? 0;
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
            $result = $assemblyDoorsCount * SecurityDoorsParametersJsonDataManipulation::getAll()["assemblyDoorsCost"];
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
                    2 * $config["distance"] * SecurityDoorsParametersJsonDataManipulation::getAll()["deliveryPricePerKm"], null
                ) : 0.0;
        }

        return 0.0;
    }

    function calculateDoorsPrice(): float
    {
        $doorsPrice = array_sum(array_map(function ($door): float {
            return $door->calculatePrice($this->currency);
        }, $this->doors ?? array()));

        $selectedDoorsLineItemsPrice = array_sum(array_map(function ($it): float {
            return $it->calculatePrice();
        }, $this->selectedDoorsLineItems ?? array()));

        return $doorsPrice + $selectedDoorsLineItemsPrice;
    }

    function calculatePrice(
        float $assemblyDoorsCalculatedPrice,
        float $calculatedDeliveryCosts,
        float $calculatedDoorsPrice,
        float $calculatedHandlesAndRosettesPrice
    ): float
    {
        return $calculatedDoorsPrice +
            $calculatedHandlesAndRosettesPrice +
            $assemblyDoorsCalculatedPrice +
            $calculatedDeliveryCosts;
    }

    function calculatePriceVat(float $priceNoVat): float
    {
        return $priceNoVat * SecurityDoorsParametersJsonDataManipulation::getAll()["vat"];
    }
}