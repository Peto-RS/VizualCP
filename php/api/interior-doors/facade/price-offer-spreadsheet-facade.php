<?php
require_once __DIR__ . '/../../common/facade/json-data-manipulation-facade.php';
require_once __DIR__ . '/../../../enums/CurrencyEnum.php';
require_once __DIR__ . '/../../../enums/DoorsWidthEnum.php';
require_once __DIR__ . '/../../../model/utils/translation-utils.php';

require __DIR__ . '/../../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

function generateSpreadSheet(PriceOfferResponse $priceOffer, string $lang): Spreadsheet
{
    $spreadsheet = loadSpreadSheet(__DIR__ . '/../../../../assets/xlsx/cenova_ponuka_Hutas_28072026.xlsx');
    addBusinessDataIntoSpreadsheet($spreadsheet, $priceOffer, $lang);
    return $spreadsheet;
}

function addBusinessDataIntoSpreadsheet(Spreadsheet $spreadsheet, PriceOfferResponse $priceOffer, string $lang): void
{
    $translationFileSk = getArrayFromJsonFile(__DIR__ . '/../../../../vue-project/src/locales/' . SupportedLanguagesEnum::SK . '.json');
    $translationFileForeign = getArrayFromJsonFile(__DIR__ . '/../../../../vue-project/src/locales/' . $lang . '.json');
    $isForeignLanguage = $lang != SupportedLanguagesEnum::SK;

    $sheetNameCalculations = 'cena_dvere';
    $currency = $priceOffer->currency;
    $currencySign = CurrencyEnum::getCurrencySign($currency);
    $parameters = ParametersJsonDataManipulation::getAll();

    $trueValue = $isForeignLanguage ?
        ($translationFileForeign["priceOfferSpreadSheet"]["boolean"]["true"] . " / " .
            $translationFileSk["priceOfferSpreadSheet"]["boolean"]["true"]) :
        $translationFileSk["priceOfferSpreadSheet"]["boolean"]["true"];

    $falseValue = $isForeignLanguage ?
        ($translationFileForeign["priceOfferSpreadSheet"]["boolean"]["false"] . " / " .
            $translationFileSk["priceOfferSpreadSheet"]["boolean"]["false"]) :
        $translationFileSk["priceOfferSpreadSheet"]["boolean"]["false"];

    $rowIdx = 1;

    /*** Contact ***/
    $sheet = $spreadsheet->getActiveSheet();

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            $translationFileSk['contact']['fullName'],
            $translationFileForeign['contact']['fullName'],
            'E' . $rowIdx,
            $rowIdx,
            32
        );
    }

    $fullName = $priceOffer->contact->fullName;
    if ($fullName) {
        $sheet->setCellValue('F' . $rowIdx, $fullName);
    }
    $rowIdx++;

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            $translationFileSk['contact']['phoneNumber'],
            $translationFileForeign['contact']['phoneNumber'],
            'E' . $rowIdx,
            $rowIdx,
            48
        );
    }

    $phoneNumber = $priceOffer->contact->phoneNumber;
    if ($phoneNumber) {
        $sheet->setCellValueExplicit('F' . $rowIdx, $phoneNumber, DataType::TYPE_STRING);
    }
    $rowIdx++;

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            $translationFileSk['priceOfferSpreadSheet']['address'],
            $translationFileForeign['priceOfferSpreadSheet']['address'],
            'E' . $rowIdx,
            $rowIdx,
            32
        );
    }
    $address = getFormattedAddress($priceOffer->address);
    if ($address) {
        $sheet->setCellValue('F' . $rowIdx, $address);
    }
    $rowIdx++;

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            $translationFileSk['contact']['email'],
            $translationFileForeign['contact']['email'],
            'E' . $rowIdx,
            $rowIdx,
            32
        );
    }
    $email = $priceOffer->contact->email;
    if ($email) {
        $sheet->setCellValue('F' . $rowIdx, $email);
    }
    $rowIdx++;

    $note = $priceOffer->note;
    if ($email) {
        $sheet->setCellValue('F' . $rowIdx, $note);
    }

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['title']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['title']),
            'A5',
            $rowIdx,
            32,
            true
        );

        fillCellWithTranslation(
            $sheet,
            $translationFileSk['priceOfferSpreadSheet']['material'],
            $translationFileForeign['priceOfferSpreadSheet']['material'],
            'E5',
            $rowIdx,
            32
        );
    }

    $rowIdx++;

    /*** Doors ***/
    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['doorsTable']['measurement']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['doorsTable']['measurement']),
            'A6',
            $rowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['doorsTable']['model']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['doorsTable']['model']),
            'B6',
            $rowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['doorsTable']['material']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['doorsTable']['material']),
            'C6',
            $rowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['doorsTable']['glass']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['doorsTable']['glass']),
            'D6',
            $rowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['pricePerUnit']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['pricePerUnit']),
            'E6',
            $rowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['doorsTable']['frame']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['doorsTable']['frame']),
            'F6',
            $rowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['count']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['count']),
            'G6',
            $rowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['priceWoVat']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['priceWoVat']),
            'H6',
            $rowIdx,
            32
        );
    }

    $doors = $priceOffer->doors;
    $selectedDoorsLineItems = $priceOffer->selectedDoorsLineItems;
    $rowIdx++;
    prefillDoorsSection($sheet, $rowIdx, $trueValue);

    $doorsCount = count($doors);
    $selectedDoorsLineItemsCount = count($selectedDoorsLineItems);
    $totalDoors = $doorsCount + $selectedDoorsLineItemsCount;
    $rowCountToInsert = max(0, $totalDoors - 9);

    if ($rowCountToInsert > 0) {
        $sheet->insertNewRowBefore(16, $rowCountToInsert);
    }

    foreach ($doors as $door) {
        $sheet->setCellValue("A$rowIdx", DoorsWidthEnum::getWidthString($door->width) ?: 60);
        $sheet->setCellValue("B$rowIdx", $door->type);

        if ($isForeignLanguage && $door->material) {
            fillCellWithTranslation(
                $sheet,
                getMaterialTranslation($translationFileSk, $door->material),
                getMaterialTranslation($translationFileForeign, $door->material),
                "C$rowIdx",
                $rowIdx,
                32
            );
        } else {
            $sheet->setCellValue("C$rowIdx", $door->material ? getMaterialTranslation($translationFileSk, $door->material) : "");
        }
        $sheet->setCellValue("E$rowIdx", "=IFERROR(VLOOKUP(B$rowIdx, $sheetNameCalculations!A:C, 3, FALSE), 0)+IF(A$rowIdx<=69, 0,IF(A$rowIdx<=79, surcharge70,IF(A$rowIdx<=89, surcharge80, surcharge90)))");
        $sheet->setCellValue("F$rowIdx", $door->isDoorFrameEnabled ? $trueValue : $falseValue);
        $sheet->setCellValue("G$rowIdx", 1);
        $sheet->setCellValue(
            "H$rowIdx",
            "=IF(ISBLANK(B$rowIdx),0, G$rowIdx*E$rowIdx+IF(F$rowIdx=\"$trueValue\", IF(B$rowIdx=\"v1\", door_frame_sale, door_frame), 0))"
        );
        $rowIdx = $rowIdx + 1;
    }

    foreach ($selectedDoorsLineItems as $door) {
        $sheet->setCellValue("A$rowIdx", DoorsWidthEnum::getWidthString($door->width) ?: 60);
        $sheet->setCellValue("B$rowIdx", $door->name);
        $sheet->setCellValue("E$rowIdx", "=$door->price+IF(A$rowIdx<=69, 0,IF(A$rowIdx<=79, surcharge70,IF(A$rowIdx<=89, surcharge80, surcharge90)))");
        $sheet->setCellValue("F$rowIdx", $door->isDoorFrameEnabled ? $trueValue : $falseValue);
        $sheet->setCellValue("G$rowIdx", 1);
        $sheet->setCellValue(
            "H$rowIdx",
            "=IF(ISBLANK(B$rowIdx),0, G$rowIdx*E$rowIdx+IF(F$rowIdx=\"$trueValue\", IF(B$rowIdx=\"v1\", door_frame_sale, door_frame), 0))"
        );

        $rowIdx = $rowIdx + 1;
    }

    /*** Handles and rosettes ***/
    $rowIdx = max($rowIdx + 2, 18);

    if ($isForeignLanguage) {
        $handlesHeaderRowIdx = $rowIdx - 1;
        fillCellWithTranslation(
            $sheet,
            $translationFileSk['priceOfferSpreadSheet']['handlesWithUrl'],
            $translationFileForeign['priceOfferSpreadSheet']['handlesWithUrl'],
            'A' . $handlesHeaderRowIdx,
            $handlesHeaderRowIdx,
            32,
            true
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['pricePerUnit']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['pricePerUnit']),
            'F' . $handlesHeaderRowIdx,
            $handlesHeaderRowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['count']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['count']),
            'G' . $handlesHeaderRowIdx,
            $handlesHeaderRowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk['priceOfferSpreadSheet']['priceWoVat']),
            mb_strtoupper($translationFileForeign['priceOfferSpreadSheet']['priceWoVat']),
            'H' . $handlesHeaderRowIdx,
            $handlesHeaderRowIdx,
            32
        );
    }

    //handle from configurator
    $handleId = $priceOffer->handle && $priceOffer->handle->id;
    if ($handleId) {
        if ($isForeignLanguage) {
            fillCellWithTranslation(
                $sheet,
                $translationFileSk["db"]["handles"][$priceOffer->handle->id]["label"],
                $translationFileForeign["db"]["handles"][$priceOffer->handle->id]["label"],
                "A$rowIdx",
                $rowIdx,
                32
            );
        } else {
            $sheet->setCellValue("A$rowIdx", $translationFileSk["db"]["handles"][$priceOffer->handle->id]["label"]);
        }

        $handleDb = HandlesJsonDataManipulation::findByIdOrNull($priceOffer->handle->id);
        $sheet->setCellValue("F$rowIdx", '=' . ($handleDb['price'] ?? 0) . '*currencyFactor');
        $sheet->setCellValue("G$rowIdx", $priceOffer->handle->count);
        $sheet->setCellValue("H$rowIdx", "=F$rowIdx*G$rowIdx");
        $rowIdx = $rowIdx + 1;
    }

    //custom handle
    $customHandle = $priceOffer->customHandle;
    if ($customHandle && $customHandle->name) {
        if ($handleId) {
            $sheet->insertNewRowBefore($rowIdx);
        }
        $sheet->mergeCells('A' . $rowIdx . ':E' . $rowIdx);

        $sheet->setCellValue("A$rowIdx", $customHandle->name);

        if ($customHandle->price) {
            $sheet->setCellValue("F$rowIdx", $customHandle->price);
        }

        if ($customHandle->count) {
            $sheet->setCellValue("G$rowIdx", $customHandle->count);
        }

        $sheet->setCellValue("H$rowIdx", "=F$rowIdx*G$rowIdx");
        $rowIdx = $rowIdx + 1;
    }

    foreach ($priceOffer->rosettes as $rosette) {
        if ($isForeignLanguage) {
            fillCellWithTranslation(
                $sheet,
                $translationFileSk["db"]["rosettes"][$rosette->id]["label"],
                $translationFileForeign["db"]["rosettes"][$rosette->id]["label"],
                "A$rowIdx",
                $rowIdx,
                32
            );
        }

        $count = $rosette->count;
        if ($count) {
            $sheet->setCellValue("G" . $rowIdx, $count);
        }

        $rowIdx = $rowIdx + 1;
    }

    $rowCountToInsert = insertLineItems($priceOffer->rosettesLineItems, $sheet, $rowIdx);
    $rowIdx = $rowIdx + 1 + $rowCountToInsert;

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            $translationFileSk["handles"]["assemblyHandlesRosettes"],
            $translationFileForeign["handles"]["assemblyHandlesRosettes"],
            "A$rowIdx",
            $rowIdx,
            32
        );
    }

    $assemblyHandlesRosettesCount = $priceOffer->assemblyHandlesRosettesCount;
    if ($assemblyHandlesRosettesCount) {
        $sheet->setCellValue('G' . $rowIdx, $assemblyHandlesRosettesCount);
    }

    /*** Delivery ***/
    $rowIdx = $rowIdx + 3;

    if ($isForeignLanguage) {
        $deliveryHeaderRowIdx = $rowIdx - 1;

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["accordionHeaders"]["delivery"]),
            mb_strtoupper($translationFileForeign["accordionHeaders"]["delivery"]),
            "A$deliveryHeaderRowIdx",
            $deliveryHeaderRowIdx,
            32,
            true
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["city"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["city"]),
            "F$deliveryHeaderRowIdx",
            $deliveryHeaderRowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["count"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["count"]),
            "G$deliveryHeaderRowIdx",
            $deliveryHeaderRowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["priceWoVat"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["priceWoVat"]),
            "H$deliveryHeaderRowIdx",
            $deliveryHeaderRowIdx,
            32
        );

        $translationParams = array(
            "price" => formatPrice(currencyRateTransformation($currency, $parameters["deliveryPricePerKm"], null), $currencySign)
        );

        fillCellWithTranslation(
            $sheet,
            $translationFileSk["delivery"]["district"] . '. ' . replaceTranslationParams($translationFileSk["delivery"]["note"], $translationParams),
            $translationFileForeign["delivery"]["district"] . '. ' . replaceTranslationParams($translationFileForeign["delivery"]["note"], $translationParams),
            "A$rowIdx",
            $rowIdx,
            64
        );
    }

    $district = $priceOffer->address->district;
    $deliveryCountry = $priceOffer->address->deliveryCountry;
    if ($district) {
        $districtDb = DistrictsJsonDataManipulation::findByIdAndCountryOrNull($district, $deliveryCountry);

        if ($isForeignLanguage) {
            fillCellWithTranslation(
                $sheet,
                getDistrictLabel($districtDb, $translationFileSk),
                getDistrictLabel($districtDb, $translationFileForeign),
                'F' . $rowIdx,
                $rowIdx,
                32
            );
        } else {
            $sheet->setCellValue('F' . $rowIdx, $districtDb ? $districtDb['label'] : '');
        }
    }

    $deliveryCost = $priceOffer->deliveryPrice;
    if ($deliveryCost) {
        $sheet->setCellValue('G' . $rowIdx, $deliveryCost);
    }
    $rowIdx++;

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            $translationFileSk["priceOfferSpreadSheet"]["deliveryCountry"],
            $translationFileForeign["priceOfferSpreadSheet"]["deliveryCountry"],
            "A$rowIdx",
            $rowIdx,
            32
        );
    }

    $deliveryCountry = $priceOffer->address->deliveryCountry;
    if ($deliveryCountry) {
        if ($isForeignLanguage)
            fillCellWithTranslation(
                $sheet,
                $translationFileSk["db"]["deliveryCountries"][$priceOffer->address->deliveryCountry],
                $translationFileForeign["db"]["deliveryCountries"][$priceOffer->address->deliveryCountry],
                "F$rowIdx",
                $rowIdx,
                32
            );
        else
            $sheet->setCellValue('F' . $rowIdx, $translationFileSk["db"]["deliveryCountries"][$priceOffer->address->deliveryCountry]);
    }

    $rowIdx++;

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            $translationFileSk["delivery"]["street"],
            $translationFileForeign["delivery"]["street"],
            "A$rowIdx",
            $rowIdx,
            32
        );
    }

    $street = $priceOffer->address->street;
    if ($street) {
        $sheet->setCellValue('F' . $rowIdx, $street);
    }

    $rowIdx++;

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            $translationFileSk["delivery"]["streetNumber"],
            $translationFileForeign["delivery"]["streetNumber"],
            "A$rowIdx",
            $rowIdx,
            32
        );
    }

    $streetNumber = $priceOffer->address->streetNumber;
    if ($streetNumber) {
        $sheet->setCellValue('F' . $rowIdx, $streetNumber);
    }

    $rowIdx++;

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            $translationFileSk["delivery"]["city"],
            $translationFileForeign["delivery"]["city"],
            "A$rowIdx",
            $rowIdx,
            32
        );
    }

    $city = $priceOffer->address->city;
    if ($city) {
        $sheet->setCellValue('F' . $rowIdx, $city);
    }

    $rowIdx++;

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            $translationFileSk["delivery"]["zipCode"],
            $translationFileForeign["delivery"]["zipCode"],
            "A$rowIdx",
            $rowIdx,
            32
        );
    }

    $zipCode = $priceOffer->address->zipCode;
    if ($zipCode) {
        $sheet->setCellValue('F' . $rowIdx, $zipCode);
    }

    /*** Assembly of doors ***/
    $rowIdx = $rowIdx + 3;

    if ($isForeignLanguage) {
        $assemblyDoorsRowIdx = $rowIdx - 1;

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["accordionHeaders"]["assemblyDoors"]),
            mb_strtoupper($translationFileForeign["accordionHeaders"]["assemblyDoors"]),
            "A$assemblyDoorsRowIdx",
            $assemblyDoorsRowIdx,
            32,
            true
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["count"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["count"]),
            "G$assemblyDoorsRowIdx",
            $assemblyDoorsRowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["priceWoVat"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["priceWoVat"]),
            "H$assemblyDoorsRowIdx",
            $assemblyDoorsRowIdx,
            32
        );

        $translationParams = array(
            "assemblyDoorsCost" => formatPrice($parameters["assemblyDoorsCost"], $currencySign),
            "assemblyDoorsCostMin" => formatPrice($parameters["assemblyDoorsCostMin"], $currencySign)
        );

        fillCellWithTranslation(
            $sheet,
            replaceTranslationParams($translationFileSk["assemblyDoors"]["note"], $translationParams),
            replaceTranslationParams($translationFileForeign["assemblyDoors"]["note"], $translationParams),
            "A$rowIdx",
            $rowIdx,
            96
        );
    }

    $assemblyDoorsCount = $priceOffer->assemblyDoorsCount;
    if ($assemblyDoorsCount) {
        $sheet->setCellValue('G' . $rowIdx, $assemblyDoorsCount);
    }

    /*** Aesthetic accessories ***/
    $rowIdx = $rowIdx + 3;

    if ($isForeignLanguage) {
        $aestheticAccessoriesRowIdx = $rowIdx - 1;

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["accordionHeaders"]["aestheticAccessories"]),
            mb_strtoupper($translationFileForeign["accordionHeaders"]["aestheticAccessories"]),
            "A$aestheticAccessoriesRowIdx",
            $aestheticAccessoriesRowIdx,
            32,
            true
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["pricePerUnit"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["pricePerUnit"]),
            "F$aestheticAccessoriesRowIdx",
            $aestheticAccessoriesRowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["count"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["count"]),
            "G$aestheticAccessoriesRowIdx",
            $aestheticAccessoriesRowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["priceWoVat"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["priceWoVat"]),
            "H$aestheticAccessoriesRowIdx",
            $aestheticAccessoriesRowIdx,
            32
        );
    }

    foreach ($priceOffer->aestheticAccessories as $item) {
        if ($isForeignLanguage) {
            fillCellWithTranslation(
                $sheet,
                $translationFileSk["db"]["aestheticAccessories"][$item->id]["label"],
                $translationFileForeign["db"]["aestheticAccessories"][$item->id]["label"],
                "A$rowIdx",
                $rowIdx,
                32
            );
        }

        $selectedPrice = $item->selectedPrice;
        if ($selectedPrice) {
            $sheet->setCellValue("F" . $rowIdx, $selectedPrice);
        }

        $count = $item->count;
        if ($count) {
            $sheet->setCellValue("G" . $rowIdx, $count);
        }

        $rowIdx = $rowIdx + 1;
    }

    $rowCountToInsert = insertLineItems($priceOffer->aestheticAccessoriesLineItems, $sheet, $rowIdx);
    $rowIdx = $rowIdx + $rowCountToInsert;

    /*** Technical surcharges ***/
    $rowIdx = $rowIdx + 3;
    if ($isForeignLanguage) {
        $technicalSurchargesRowIdx = $rowIdx - 1;

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["accordionHeaders"]["technicalSurcharges"]),
            mb_strtoupper($translationFileForeign["accordionHeaders"]["technicalSurcharges"]),
            "A$technicalSurchargesRowIdx",
            $technicalSurchargesRowIdx,
            32,
            true
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["pricePerUnit"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["pricePerUnit"]),
            "F$technicalSurchargesRowIdx",
            $technicalSurchargesRowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["count"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["count"]),
            "G$technicalSurchargesRowIdx",
            $technicalSurchargesRowIdx,
            32
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["priceWoVat"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["priceWoVat"]),
            "H$technicalSurchargesRowIdx",
            $technicalSurchargesRowIdx,
            32
        );
    }

    foreach ($priceOffer->technicalSurcharges as $item) {
        if ($isForeignLanguage) {
            $translationParams = array(
                "price" => formatPrice($item->configuredPrice, $currencySign)
            );

            fillCellWithTranslation(
                $sheet,
                replaceTranslationParams($translationFileSk["db"]["technicalSurcharges"][$item->id]["label"], $translationParams),
                replaceTranslationParams($translationFileForeign["db"]["technicalSurcharges"][$item->id]["label"], $translationParams),
                "A$rowIdx",
                $rowIdx,
                170
            );
        }

        $count = $item->count;
        if ($count) {
            $sheet->setCellValue("G" . $rowIdx, $count);
        }

        $rowIdx = $rowIdx + 1;
    }

    $rowCountToInsert = insertLineItems($priceOffer->technicalSurchargesLineItems, $sheet, $rowIdx);
    $rowIdx = $rowIdx + $rowCountToInsert;

    /*** Special surcharges ***/
    $rowIdx = $rowIdx + 3;

    if ($isForeignLanguage) {
        $specialSurchargesRowIdx = $rowIdx - 1;

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["accordionHeaders"]["specialSurcharges"]),
            mb_strtoupper($translationFileForeign["accordionHeaders"]["specialSurcharges"]),
            "A$specialSurchargesRowIdx",
            $specialSurchargesRowIdx,
            64,
            true
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["assembly"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["assembly"]),
            "F$specialSurchargesRowIdx",
            $specialSurchargesRowIdx
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["count"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["count"]),
            "G$specialSurchargesRowIdx",
            $specialSurchargesRowIdx
        );

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["priceWoVat"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["priceWoVat"]),
            "H$specialSurchargesRowIdx",
            $specialSurchargesRowIdx
        );
    }

    foreach ($priceOffer->specialSurcharges as $item) {
        $specialSurchargeDb = SpecialSurchargesJsonDataManipulation::findByIdOrNull($item->id);

        $configuredPrice = $specialSurchargeDb ? $specialSurchargeDb['price'] : 0;
        $configuredPriceCurrencyFactor = $item->configuredPrice;
        $assemblyCosts = $specialSurchargeDb ? $specialSurchargeDb['assemblyCosts'] : 0;
        $assemblyCostsCurrencyFactor = $item->assemblyCosts;

        if ($isForeignLanguage) {
            $translationParams = array(
                'price' => formatPrice($configuredPriceCurrencyFactor, $currencySign)
            );

            $translationParamsAssembly = array(
                'price' => formatPrice($assemblyCostsCurrencyFactor, $currencySign)
            );

            fillCellWithTranslation(
                $sheet,
                replaceTranslationParams($translationFileSk["db"]["specialSurcharges"][$item->id]["label"], $translationParams) . ' ' . replaceTranslationParams($translationFileSk["db"]["specialSurcharges"][$item->id]["labelAssembly"], $translationParamsAssembly),
                replaceTranslationParams($translationFileForeign["db"]["specialSurcharges"][$item->id]["label"], $translationParams) . ' ' . replaceTranslationParams($translationFileForeign["db"]["specialSurcharges"][$item->id]["labelAssembly"], $translationParamsAssembly),
                "A$rowIdx",
                $rowIdx,
                64
            );
        }

        $isAssemblySelected = $item->isAssemblySelected;
        $sheet->setCellValue("F" . $rowIdx, $isAssemblySelected ? $trueValue : $falseValue);

        $count = $item->count;
        if ($count) {
            $sheet->setCellValue("G" . $rowIdx, $count);
        }

        $sheet->setCellValue(
            "H$rowIdx",
            "=G$rowIdx*(($configuredPrice*currencyFactor)+IF(F$rowIdx=\"$trueValue\",$assemblyCosts*currencyFactor,0))"
        );

        $rowIdx = $rowIdx + 1;
    }

    $rowCountToInsert = insertLineItems($priceOffer->specialSurchargesLineItems, $sheet, $rowIdx);
    $rowIdx = $rowIdx + $rowCountToInsert;
    $rowIdx = $rowIdx + 2;

    $rowCountToInsert = insertCampaigns(
        $sheet,
        $rowIdx,
        $isForeignLanguage,
        $priceOffer,
        $translationFileSk,
        $translationFileForeign
    );
    $rowIdx = $rowIdx + $rowCountToInsert;
    $rowIdx = $rowIdx + 1;

    if ($isForeignLanguage) {
        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["currency"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["currency"]),
            "A$rowIdx",
            $rowIdx,
            48,
            true,
            Color::COLOR_WHITE,
            24,
            12
        );
    }

    $sheet->setCellValue(
        "H$rowIdx",
        $priceOffer->currency
    );

    $currencyDefinedName = $spreadsheet->getDefinedName('currency');
    if ($currencyDefinedName) {
        $currencyDefinedName->setValue("'" . $sheet->getTitle() . "'!\$H\$$rowIdx");
    }

    if ($isForeignLanguage) {
        $rowIdx = $rowIdx + 1;

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["priceWoVatFinal"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["priceWoVatFinal"]),
            "A$rowIdx",
            $rowIdx,
            48,
            true,
            Color::COLOR_WHITE,
            24,
            12
        );

        $rowIdx = $rowIdx + 1;

        fillCellWithTranslation(
            $sheet,
            mb_strtoupper($translationFileSk["priceOfferSpreadSheet"]["priceVatFinal"]),
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["priceVatFinal"]),
            "A$rowIdx",
            $rowIdx,
            48,
            true,
            Color::COLOR_WHITE,
            24,
            12
        );
    }

    if ($isForeignLanguage) {
        $rowIdx = $rowIdx + 2;
        $sheet->setCellValue(
            'A' . $rowIdx,
            mb_strtoupper($translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["title"])
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["interiorDoors"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["interiorDoors"]["title"],
            $rowIdx,
            57
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["whiteSprayPaintedDoors"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["whiteSprayPaintedDoors"]["title"],
            $rowIdx,
            45
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["whiteLaminateDoors"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["whiteLaminateDoors"]["title"],
            $rowIdx,
            40
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["slidingDoors"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["slidingDoors"]["title"],
            $rowIdx,
            31
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["veneeredDoors"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["veneeredDoors"]["title"],
            $rowIdx,
            31
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["laminateDoors"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["laminateDoors"]["title"],
            $rowIdx,
            81
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["assembly"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["assembly"]["title"],
            $rowIdx,
            298
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["customMadeDoors"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["customMadeDoors"]["title"],
            $rowIdx,
            141
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["metalDoorFrameCladding"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["metalDoorFrameCladding"]["title"],
            $rowIdx,
            81
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["parking"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["parking"]["title"],
            $rowIdx,
            33
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["opticalDefects"]["description"],
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["opticalDefects"]["title"],
            $rowIdx,
            42
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["misc"]["1"],
            null,
            $rowIdx,
            43
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["misc"]["2"],
            null,
            $rowIdx,
            15
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["misc"]["3"],
            null,
            $rowIdx,
            19
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["misc"]["4"],
            null,
            $rowIdx,
            15
        );

        $rowIdx = $rowIdx + 1;
        fillDisclaimer(
            $sheet,
            $translationFileForeign["priceOfferSpreadSheet"]["disclaimer"]["misc"]["5"],
            null,
            $rowIdx,
            60
        );
    }
}

function loadSpreadSheet(string $filename): Spreadsheet
{
    $reader = new Xlsx();
    return $reader->load($filename);
}

function writeSpreadSheetToOutput(Spreadsheet $spreadsheet): string
{
    ob_start();
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    return ob_get_clean();
}

function getFormattedAddress(AddressResponse $address): string
{
    $parts = array_filter([
        trim($address->street . ' ' . $address->streetNumber),
        trim($address->zipCode . ' ' . $address->city),
    ]);

    return implode(', ', $parts);
}

function insertLineItems(
    array     $lineItems,
    Worksheet $sheet,
    int       $rowIdx
): int
{
    $lineItemsCount = count($lineItems);
    $rowCountToInsert = $lineItemsCount > 1 ? $lineItemsCount - 1 : 0;

    if ($rowCountToInsert > 0) {
        $sheet->insertNewRowBefore($rowIdx, $rowCountToInsert);
    }

    foreach ($lineItems as $item) {
        $name = $item->name;
        if ($name) {
            $sheet->setCellValue("A" . $rowIdx, $name);
        }

        $price = $item->price;
        if ($price) {
            $sheet->setCellValue("F" . $rowIdx, $price);
        }

        $count = $item->count;
        if ($count) {
            $sheet->setCellValue("G" . $rowIdx, $count);
        }

        $sheet->setCellValue("H$rowIdx", "=F$rowIdx*G$rowIdx");

        $sheet->mergeCells('A' . $rowIdx . ':E' . $rowIdx);
        $sheet->getStyle("A{$rowIdx}:H{$rowIdx}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFFFFF');
        $sheet->getRowDimension($rowIdx)->setRowHeight(15);

        $rowIdx = $rowIdx + 1;
    }

    return $rowCountToInsert;
}

function insertCampaigns(
    Worksheet          $sheet,
    int                $rowIdx,
    bool               $isForeignLanguage,
    PriceOfferResponse $priceOffer,
    array              $translationFileSk,
    array              $translationFileForeign
): int
{
    $campaignDetailsCount = count($priceOffer->campaignDetails);
    $rowCountToInsert = $campaignDetailsCount > 1 ? $campaignDetailsCount - 1 : 0;

    if ($rowCountToInsert > 0) {
        $sheet->insertNewRowBefore($rowIdx, $rowCountToInsert);
    }

    foreach ($priceOffer->campaignDetails as $campaignDetail) {
        $sheet->mergeCells("A$rowIdx:E$rowIdx");
        $sheet->getStyle("A$rowIdx")
            ->getAlignment()
            ->setWrapText(true);

        $sheet->getRowDimension($rowIdx)->setRowHeight(32);
        $sheet->getStyle("A$rowIdx:H$rowIdx")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('DCB946');

        if ($isForeignLanguage) {
            fillCellWithTranslation(
                $sheet,
                getTranslation($translationFileSk, $campaignDetail->translationKey),
                getTranslation($translationFileForeign, $campaignDetail->translationKey),
                "A$rowIdx",
                $rowIdx,
                32
            );
        } else {
            $sheet->setCellValue("A$rowIdx", getTranslation($translationFileSk, $campaignDetail->translationKey));
        }

        $sheet->setCellValue("F$rowIdx", "=ROUND(H$rowIdx*" . ParametersJsonDataManipulation::getAll()["vat"] . ", 2)");
        $sheet->getStyle("F$rowIdx")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue("H$rowIdx", "=ROUND(" . -$campaignDetail->discountAmount . ", 2)");
        $sheet->getStyle("H$rowIdx")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $rowIdx = $rowIdx + 1;
    }

    return $rowCountToInsert;
}

function prefillDoorsSection(
    Worksheet $sheet,
    int       $rowIdx,
    string    $trueValue
)
{
    $fixedRowsCount = 9;

    for ($i = 1; $i <= $fixedRowsCount; $i++) {
        $sheet->setCellValue("F$rowIdx", $trueValue);
        $sheet->setCellValue(
            "H$rowIdx",
            "=IF(ISBLANK(B$rowIdx),0, G$rowIdx*E$rowIdx+IF(F$rowIdx=\"$trueValue\", IF(B$rowIdx=\"v1\", door_frame_sale, door_frame), 0))"
        );

        $rowIdx = $rowIdx + 1;
    }
}

function fillCellWithTranslation(
    Worksheet $sheet,
    string    $skText,
    string    $foreignText,
    string    $cellCoordinate,
    ?int      $rowIdx = null,
    ?int      $rowHeight = null,
    ?bool     $isBold = false,
    string    $fontColor = Color::COLOR_BLACK,
    ?int      $fontSizePrimary = 11,
    ?int      $fontSizeSecondary = 9
)
{
    $sheet->getStyle($cellCoordinate)
        ->getAlignment()
        ->setWrapText(true);

    if ($rowIdx !== null && $rowHeight !== null) {
        $sheet->getRowDimension($rowIdx)->setRowHeight($rowHeight);
    }

    $richText = new RichText();

    $run1 = $richText->createTextRun($foreignText);
    $run1->getFont()
        ->setBold($isBold)
        ->setSize($fontSizePrimary)
        ->getColor()
        ->setARGB($fontColor);

    $richText->createText("\n");

    $run2 = $richText->createTextRun($skText);
    $run2->getFont()
        ->setBold($isBold)
        ->setItalic(true)
        ->setSize($fontSizeSecondary)
        ->getColor()
        ->setARGB($fontColor);

    $sheet->setCellValue($cellCoordinate, $richText);
}

function fillDisclaimer(
    Worksheet $sheet,
    string    $bodyText,
    ?string   $titleText = null,
    ?int      $rowIdx = null,
    ?int      $rowHeight = null
)
{
    if ($rowIdx !== null && $rowHeight !== null) {
        $sheet->getRowDimension($rowIdx)->setRowHeight($rowHeight);
    }

    $richText = new RichText();

    if ($titleText != null) {
        $run1 = $richText->createTextRun(mb_strtoupper($titleText));
        $run1->getFont()
            ->setBold(true)
            ->setSize(11)
            ->getColor()
            ->setARGB('FFBE433F');

        $richText->createText(': ');
    }

    $run2 = $richText->createTextRun($bodyText);
    $run2->getFont()
        ->setSize(11);

    $sheet->setCellValue("A$rowIdx", $richText);
}

function getMaterialTranslation(array $translationJson, string $materialId)
{
    $materials = $translationJson["db"]["materials"];
    if (array_key_exists($materialId, $materials["laminates"])) {
        return $materials["laminates"][$materialId]["label"];
    } else if (array_key_exists($materialId, $materials["veneers"])) {
        return $materials["veneers"][$materialId]["label"];
    } else {
        return $materialId;
    }
}

function formatPrice(?float $price, string $currencySuffix): string
{
    $price = $price ?? 0;

    // Format with up to 2 decimals
    $formatted = number_format($price, 2, ',', ' ');

    // Remove unnecessary trailing zeros
    $formatted = rtrim(rtrim($formatted, '0'), ',');

    return $formatted . ' ' . $currencySuffix;
}

function getDistrictLabel(array $districtDb, array $translationFile): string
{
    if ($districtDb && array_key_exists('translationKey', $districtDb))
        return $translationFile['db']['districts'][$districtDb['translationKey']];
    else
        return $districtDb['label'];
}

?>