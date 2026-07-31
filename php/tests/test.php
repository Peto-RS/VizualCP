<?php

use PHPUnit\Framework\TestCase;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once __DIR__ . '/../../functions.php';
require_once __DIR__ . '/../../php/enums/CurrencyEnum.php';
require_once __DIR__ . '/../api/model/cart-model.php';
require_once __DIR__ . '/../api/price-offer-spreadsheet-facade.php';
require_once __DIR__ . '/../enums/SupportedLanguagesEnum.php';

class ApiIntegrationTest extends TestCase
{
    private $configuratorUrl = 'http://localhost:8080/api-configurator.php';
    private $priceOfferUrl = 'http://localhost:8080/api.php';
    private $sessionId = 'testsession123';

    public function testPriceOffer()
    {
        $sessionId = bin2hex(random_bytes(16));

        /*
         * 1. POST /api-configurator.php
         */
        $req = array(
            "category" => "best-selling",
            "handleId" => "Nova_nerez_R",
            "material" => "lamino-jelsa",
            "type" => "v1"
        );
        $res = $this->request('POST', $this->configuratorUrl . "?lang=" . SupportedLanguagesEnum::SK, $sessionId, $req);
        $this->assertEquals("HTTP/1.0 200 OK", $res['headers'][0]);
        $resBody = json_decode($res['body'], true);

        $this->assertEquals(CurrencyEnum::EUR, $resBody['currency']);

        //prefilled values
        $this->assertEquals(1, $resBody['handle']['count']);
        $this->assertEquals(1, $resBody['assemblyDoorsCount']);
        $this->assertEquals(1, $resBody['assemblyHandlesRosettesCount']);
        $this->assertEquals(0, $resBody['technicalSurcharges'][0]['count']);
        $this->assertEquals(1, $resBody['technicalSurcharges'][1]['count']);
        $this->assertEquals(1, $resBody['technicalSurcharges'][2]['count']);

        //calculated values
        $this->assertEquals(210, $resBody['sectionsCalculatedPrice']['assemblyDoors']);
        $this->assertEquals(219, $resBody['sectionsCalculatedPrice']['doors']);
        $this->assertEquals(20, $resBody['sectionsCalculatedPrice']['handlesAndRosettes']);
        $this->assertEquals(14.2, $resBody['sectionsCalculatedPrice']['technicalSurcharges']);
        $this->assertEquals(463.2, $resBody['calculatedPrice']);
        $this->assertEquals(569.736, $resBody['calculatedPriceVat']);

        /*
         * 2. POST /api-configurator.php
         */
        $res = $this->request('POST', $this->configuratorUrl . "?lang=" . SupportedLanguagesEnum::SK, $sessionId, $req);
        $this->assertEquals("HTTP/1.0 200 OK", $res['headers'][0]);
        $resBody = json_decode($res['body'], true);

        //prefilled values
        $this->assertEquals(2, $resBody['handle']['count']);
        $this->assertEquals(2, $resBody['assemblyDoorsCount']);
        $this->assertEquals(2, $resBody['assemblyHandlesRosettesCount']);
        $this->assertEquals(0, $resBody['technicalSurcharges'][0]['count']);
        $this->assertEquals(2, $resBody['technicalSurcharges'][1]['count']);
        $this->assertEquals(2, $resBody['technicalSurcharges'][2]['count']);

        //calculated values
        $this->assertEquals(210, $resBody['sectionsCalculatedPrice']['assemblyDoors']);
        $this->assertEquals(438, $resBody['sectionsCalculatedPrice']['doors']);
        $this->assertEquals(40, $resBody['sectionsCalculatedPrice']['handlesAndRosettes']);
        $this->assertEquals(28.4, $resBody['sectionsCalculatedPrice']['technicalSurcharges']);
        $this->assertEquals(716.4, $resBody['calculatedPrice']);
        $this->assertEquals(881.17, round($resBody['calculatedPriceVat'], 2));

        /*
        * 3. POST /api-configurator.php
         */
        $req = array(
            "category" => "wien_1",
            "handleId" => "Alt_wien",
            "material" => "wien-biela",
            "type" => "W1_L2_K2_O2"
        );
        $res = $this->request('POST', $this->configuratorUrl . "?lang=" . SupportedLanguagesEnum::SK, $sessionId, $req);
        $this->assertEquals("HTTP/1.0 200 OK", $res['headers'][0]);
        $resBody = json_decode($res['body'], true);

        //prefilled values
        $this->assertEquals("Alt_wien", $resBody['handle']['id']);
        $this->assertEquals(3, $resBody['handle']['count']);
        $this->assertEquals(3, $resBody['assemblyDoorsCount']);
        $this->assertEquals(3, $resBody['assemblyHandlesRosettesCount']);
        $this->assertEquals(0, $resBody['technicalSurcharges'][0]['count']);
        $this->assertEquals(3, $resBody['technicalSurcharges'][1]['count']);
        $this->assertEquals(3, $resBody['technicalSurcharges'][2]['count']);

        //calculated values
        $this->assertEquals(210, $resBody['sectionsCalculatedPrice']['assemblyDoors']);
        $this->assertEquals(1546, $resBody['sectionsCalculatedPrice']['doors']);
        $this->assertEquals(129, $resBody['sectionsCalculatedPrice']['handlesAndRosettes']);
        $this->assertEquals(42.6, $resBody['sectionsCalculatedPrice']['technicalSurcharges']);
        $this->assertEquals(1927.6, $resBody['calculatedPrice']);
        $this->assertEquals(2370.95, round($resBody['calculatedPriceVat'], 2));

        /*
        * 3.    POST /api.php
        *       GET /api.php
        */
        $req = getArrayFromJsonFile(__DIR__ . "/resources/price-offer-request.json");
        $res = $this->request('POST', $this->priceOfferUrl, $sessionId, $req);
        $this->assertEquals("HTTP/1.0 200 OK", $res['headers'][0]);

        $res = $this->request('GET', $this->priceOfferUrl . "?currency=" . CurrencyEnum::EUR . "&lang=" . SupportedLanguagesEnum::SK, $sessionId);
        $resBody = json_decode($res['body'], true);

        $this->assertEquals('City', $resBody['priceOffer']['address']['city']);
        $this->assertEquals('banska-bystrica', $resBody['priceOffer']['address']['district']);
        $this->assertEquals('Street', $resBody['priceOffer']['address']['street']);
        $this->assertEquals('1', $resBody['priceOffer']['address']['streetNumber']);
        $this->assertEquals('97901', $resBody['priceOffer']['address']['zipCode']);

        $this->assertEquals(true, $resBody['priceOffer']['specialSurcharges'][0]['isAssemblySelected']);

        $this->assertEquals(214, $resBody['priceOffer']['deliveryPrice']);
        $this->assertEquals(2889.6, $resBody['priceOffer']['calculatedPrice']);
        $this->assertEquals(3554.21, round($resBody['priceOffer']['calculatedPriceVat'], 2));

        /*
        * 4.    generateSpreadSheet currency=EUR
        */
        $priceOfferReq = new PriceOfferRequest($resBody['priceOffer']);
        $spreadsheet = generateSpreadSheet(PriceOffer::fromRequest($priceOfferReq, SupportedLanguagesEnum::SK)->toResponse(CurrencyEnum::EUR), SupportedLanguagesEnum::SK);

        //save to disk
        $writer = new Xlsx($spreadsheet);
        $writer->save(__DIR__ . '/test-price-offer.xlsx');

        $rowIdx = 1;
        $this->assertEquals("Meno", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx++)->getValue());
        $this->assertEquals("+42111111111", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx++)->getValue());
        $this->assertEquals("Street 1, 97901 City", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx++)->getValue());
        $this->assertEquals("aa@aa.sk", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx++)->getValue());
        $this->assertEquals("Note", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx++)->getValue());
        $rowIdx++;

        $this->assertDoorsRow($spreadsheet, strval($rowIdx++), "60", "v1", "Jelša", "139", "Áno", "1", "219");
        $this->assertDoorsRow($spreadsheet, strval($rowIdx++), "60", "v1", "Jelša", "139", "Áno", "1", "219");
        $this->assertDoorsRow($spreadsheet, strval($rowIdx++), "90", "W1_L2_K2_O2", "Striekaná biela", "1018", "Nie", "1", "1018");
        $this->assertDoorsRow($spreadsheet, strval($rowIdx++), "70", "Custom door 1", "", "103", "Áno", "1", "202");
        $this->assertDoorsRow($spreadsheet, strval($rowIdx++), "80", "Custom door 2", "", "106", "Nie", "1", "106");
        $rowIdx = $rowIdx + 6;

        $this->assertEquals("ALT WIEN Štít", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("38", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getCalculatedValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("114", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Custom handle", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Rozeta - BB štandardný interiérový zámok", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("0", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getCalculatedValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("0", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Rozeta - WC zámok", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("11", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getCalculatedValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("11", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Rozeta - FAB zámok", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("0", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getCalculatedValue());
        $this->assertEquals("", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("0", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Custom rosette", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("10", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("10", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Custom rosette 2", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("10", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("20", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Montáž kľučiek a roziet", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("5", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getCalculatedValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("15", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx)->getCalculatedValue());

        $rowIdx = $rowIdx + 3;

        $this->assertEquals("Okresné mesto. Cena prepravy je 0,5 € za km. Preprava sa počíta od výroby v obci Čachtice.", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("Banská Bystrica", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("214", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("214", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Slovensko", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx++)->getCalculatedValue());
        $this->assertEquals("Street", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx++)->getCalculatedValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx++)->getCalculatedValue());
        $this->assertEquals("City", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx++)->getCalculatedValue());
        $this->assertEquals("97901", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx++)->getCalculatedValue());
        $rowIdx = $rowIdx + 2;

        $this->assertEquals("Akciová montáž dverí so zárubňou 35 € / kus. Akcia platí pri počte 6 a viac kusov. Minimálna suma na dvoch montážnikov je 210 €.", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getCalculatedValue());
        $rowIdx = $rowIdx + 3;

        $this->assertEquals("12", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getCalculatedValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("12", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx)->getCalculatedValue());
        $rowIdx = $rowIdx + 3;

        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("5", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("5", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx)->getCalculatedValue());
        $rowIdx = $rowIdx + 6;

        $this->assertEquals("Aesthetic accessory 1", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Aesthetic accessory 2", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("4", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Aesthetic accessory 3", $spreadsheet->getActiveSheet()->getCell('A' . $rowIdx)->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("9", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx)->getCalculatedValue());
        $rowIdx = $rowIdx + 4;

        $this->assertEquals("10", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getCalculatedValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("30", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals(4.2, round(floatval($spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getCalculatedValue()), 2));
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals(12.6, round(floatval($spreadsheet->getActiveSheet()->getCell('H' . $rowIdx)->getCalculatedValue()), 2));

        $this->assertEquals("Aesthetic accessory 1", $spreadsheet->getActiveSheet()->getCell('A48')->getValue());
        $rowIdx = 61;
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Aesthetic accessory 2", $spreadsheet->getActiveSheet()->getCell('A49')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("4", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx++)->getCalculatedValue());

        $this->assertEquals("Aesthetic accessory 3", $spreadsheet->getActiveSheet()->getCell('A50')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('F' . $rowIdx)->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G' . $rowIdx)->getValue());
        $this->assertEquals("9", $spreadsheet->getActiveSheet()->getCell('H' . $rowIdx)->getCalculatedValue());

        $this->assertEquals("Áno", $spreadsheet->getActiveSheet()->getCell('F66')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G66')->getValue());
        $this->assertEquals("128", $spreadsheet->getActiveSheet()->getCell('H66')->getCalculatedValue());

        $this->assertEquals("Áno", $spreadsheet->getActiveSheet()->getCell('F73')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G73')->getValue());
        $this->assertEquals("300", $spreadsheet->getActiveSheet()->getCell('H73')->getCalculatedValue());

        $this->assertEquals("Special surcharges 1", $spreadsheet->getActiveSheet()->getCell('A86')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F86')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G86')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('H86')->getCalculatedValue());

        $this->assertEquals("Special surcharges 2", $spreadsheet->getActiveSheet()->getCell('A87')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('F87')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('G87')->getValue());
        $this->assertEquals("4", $spreadsheet->getActiveSheet()->getCell('H87')->getCalculatedValue());

        $this->assertEquals("Special surcharges 3", $spreadsheet->getActiveSheet()->getCell('A88')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('F88')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G88')->getValue());
        $this->assertEquals("9", $spreadsheet->getActiveSheet()->getCell('H88')->getCalculatedValue());

        $this->assertEquals(CurrencyEnum::EUR, $spreadsheet->getActiveSheet()->getCell('H91')->getValue());
        $this->assertEquals(2889.6, round(floatval($spreadsheet->getActiveSheet()->getCell('H92')->getCalculatedValue()), 2));
        $this->assertEquals(3554.21, round(floatval($spreadsheet->getActiveSheet()->getCell('H93')->getCalculatedValue()), 2));

        /*
        * 5.    generateSpreadSheet currency=CZK
        */
        $spreadsheet = generateSpreadSheet(PriceOffer::fromRequest($priceOfferReq, SupportedLanguagesEnum::SK)->toResponse(CurrencyEnum::CZK), SupportedLanguagesEnum::EN);

        //save to disk
        $writer = new Xlsx($spreadsheet);
        $writer->save(__DIR__ . '/test-price-offer-CZK.xlsx');

        $this->assertEquals(CurrencyEnum::CZK, $spreadsheet->getActiveSheet()->getCell('H91')->getValue());
        $this->assertEquals(63899.32, round(floatval($spreadsheet->getActiveSheet()->getCell('H92')->getCalculatedValue()), 2));
        $this->assertEquals(78596.16, round(floatval($spreadsheet->getActiveSheet()->getCell('H93')->getCalculatedValue()), 2));

        /*
        * 6.    generateSpreadSheet for all supported languages
        */
        foreach (SupportedLanguagesEnum::getAll() as $lang) {
            $spreadsheet = generateSpreadSheet(PriceOffer::fromRequest($priceOfferReq, SupportedLanguagesEnum::SK)->toResponse(CurrencyEnum::EUR), $lang);

            $this->assertEquals(CurrencyEnum::EUR, $spreadsheet->getActiveSheet()->getCell('H91')->getValue());
            $this->assertEquals(2889.6, round(floatval($spreadsheet->getActiveSheet()->getCell('H92')->getCalculatedValue()), 2));
            $this->assertEquals(3554.21, round(floatval($spreadsheet->getActiveSheet()->getCell('H93')->getCalculatedValue()), 2));
        }
    }

    function assertDoorsRow(
        \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet,
        string                                $index,
        string                                $doorsWidth,
        string                                $model,
        string                                $material,
        string                                $price,
        string                                $frame,
        string                                $count,
        string                                $priceTotal
    )
    {
        $this->assertEquals($doorsWidth, $spreadsheet->getActiveSheet()->getCell('A' . $index)->getValue());
        $this->assertEquals($model, $spreadsheet->getActiveSheet()->getCell('B' . $index)->getValue());
        $this->assertEquals($material, $spreadsheet->getActiveSheet()->getCell('C' . $index)->getValue());
        $this->assertEquals($price, $spreadsheet->getActiveSheet()->getCell('E' . $index)->getCalculatedValue());
        $this->assertEquals($frame, $spreadsheet->getActiveSheet()->getCell('F' . $index)->getValue());
        $this->assertEquals($count, $spreadsheet->getActiveSheet()->getCell('G' . $index)->getValue());
        $this->assertEquals($priceTotal, $spreadsheet->getActiveSheet()->getCell('H' . $index)->getCalculatedValue());
    }

    private function request($method, $url, $sessionId = null, $body = null): array
    {
        $cookieSessionId = $sessionId ?: $this->sessionId;
        $headers = "Cookie: PHPSESSID=$cookieSessionId\r\n";

        if ($method === 'POST') {
            $headers .= "Content-Type: application/json\r\n";
        }

        $options = [
            'http' => [
                'method' => $method,
                'header' => $headers,
                'ignore_errors' => true // IMPORTANT: allows reading 4xx/5xx responses
            ]
        ];

        if ($body !== null) {
            $options['http']['content'] = json_encode($body);
        }

        $context = stream_context_create($options);

        $response = file_get_contents($url, false, $context);

        return [
            'body' => $response,
            'headers' => $http_response_header ?? []
        ];
    }
}