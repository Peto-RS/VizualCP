<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../functions.php';
require_once __DIR__ . '/../../php/enums/CurrencyEnum.php';
require_once __DIR__ . '/../api/model/cart-model.php';
require_once __DIR__ . '/../api/price-offer-spreadsheet-facade.php';

class ApiIntegrationTest extends TestCase
{
    private $configuratorUrl = 'http://localhost:8080/api-configurator.php';
    private $priceOfferSaveUrl = 'http://localhost:8080/api.php';
    private $priceOfferGetUrl = 'http://localhost:8080/api.php';
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
        $res = $this->request('POST', $this->configuratorUrl, $sessionId, $req);
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
        $res = $this->request('POST', $this->configuratorUrl, $sessionId, $req);
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
        $res = $this->request('POST', $this->configuratorUrl, $sessionId, $req);
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
        $res = $this->request('POST', $this->priceOfferSaveUrl, $sessionId, $req);
        $this->assertEquals("HTTP/1.0 200 OK", $res['headers'][0]);

        $res = $this->request('GET', $this->priceOfferGetUrl . "?currency=" . CurrencyEnum::EUR, $sessionId);
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
        * 4.    generateSpreadSheet
        */
        $spreadsheet = generateSpreadSheet(PriceOffer::fromRequest(new PriceOfferRequest($resBody['priceOffer']))->toResponse(CurrencyEnum::EUR));
        $this->assertEquals("Meno", $spreadsheet->getActiveSheet()->getCell('F1')->getValue());
        $this->assertEquals("+42111111111", $spreadsheet->getActiveSheet()->getCell('F2')->getValue());
        $this->assertEquals("Street 1, 97901 City", $spreadsheet->getActiveSheet()->getCell('F3')->getValue());
        $this->assertEquals("aa@aa.sk", $spreadsheet->getActiveSheet()->getCell('F4')->getValue());
        $this->assertEquals("Note", $spreadsheet->getActiveSheet()->getCell('F5')->getValue());

        $this->assertDoorsRow($spreadsheet, "7", "60", "v1", "Jelša", "139", "Áno", "1", "219");
        $this->assertDoorsRow($spreadsheet, "8", "60", "v1", "Jelša", "139", "Áno", "1", "219");
        $this->assertDoorsRow($spreadsheet, "9", "90", "W1_L2_K2_O2", "Striekaná biela", "1018", "Nie", "1", "1018");
        $this->assertDoorsRow($spreadsheet, "10", "70", "Custom door 1", "", "100", "Áno", "1", "202");
        $this->assertDoorsRow($spreadsheet, "11", "80", "Custom door 2", "", "100", "Nie", "1", "106");

        $this->assertEquals("ALT WIEN Štít", $spreadsheet->getActiveSheet()->getCell('A18')->getValue());
        $this->assertEquals("38", $spreadsheet->getActiveSheet()->getCell('F18')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G18')->getValue());
        $this->assertEquals("114", $spreadsheet->getActiveSheet()->getCell('H18')->getCalculatedValue());

        $this->assertEquals("Custom handle", $spreadsheet->getActiveSheet()->getCell('A19')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F19')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('G19')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('H19')->getCalculatedValue());

        $this->assertEquals("Rozeta - BB štandardný interiérový zámok", $spreadsheet->getActiveSheet()->getCell('A20')->getValue());
        $this->assertEquals("0", $spreadsheet->getActiveSheet()->getCell('F20')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G20')->getValue());
        $this->assertEquals("0", $spreadsheet->getActiveSheet()->getCell('H20')->getCalculatedValue());

        $this->assertEquals("Rozeta - WC zámok", $spreadsheet->getActiveSheet()->getCell('A21')->getValue());
        $this->assertEquals("11", $spreadsheet->getActiveSheet()->getCell('F21')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G21')->getValue());
        $this->assertEquals("11", $spreadsheet->getActiveSheet()->getCell('H21')->getCalculatedValue());

        $this->assertEquals("Rozeta - FAB zámok", $spreadsheet->getActiveSheet()->getCell('A22')->getValue());
        $this->assertEquals("0", $spreadsheet->getActiveSheet()->getCell('F22')->getValue());
        $this->assertEquals("", $spreadsheet->getActiveSheet()->getCell('G22')->getValue());
        $this->assertEquals("0", $spreadsheet->getActiveSheet()->getCell('H22')->getCalculatedValue());

        $this->assertEquals("Custom rosette", $spreadsheet->getActiveSheet()->getCell('A23')->getValue());
        $this->assertEquals("10", $spreadsheet->getActiveSheet()->getCell('F23')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G23')->getValue());
        $this->assertEquals("10", $spreadsheet->getActiveSheet()->getCell('H23')->getCalculatedValue());

        $this->assertEquals("Custom rosette 2", $spreadsheet->getActiveSheet()->getCell('A24')->getValue());
        $this->assertEquals("10", $spreadsheet->getActiveSheet()->getCell('F24')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('G24')->getValue());
        $this->assertEquals("20", $spreadsheet->getActiveSheet()->getCell('H24')->getCalculatedValue());

        $this->assertEquals("Montáž kľučiek a roziet 5€", $spreadsheet->getActiveSheet()->getCell('A25')->getValue());
        $this->assertEquals("5", $spreadsheet->getActiveSheet()->getCell('F25')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G25')->getValue());
        $this->assertEquals("15", $spreadsheet->getActiveSheet()->getCell('H25')->getCalculatedValue());

        $this->assertEquals("Okresné mesto. Cena prepravy je 0,5 € za km. Preprava sa počíta od výroby v obci Čachtice.", $spreadsheet->getActiveSheet()->getCell('A28')->getValue());
        $this->assertEquals("Banská Bystrica", $spreadsheet->getActiveSheet()->getCell('F28')->getValue());
        $this->assertEquals("214", $spreadsheet->getActiveSheet()->getCell('G28')->getValue());
        $this->assertEquals("214", $spreadsheet->getActiveSheet()->getCell('H28')->getCalculatedValue());

        $this->assertEquals("Street", $spreadsheet->getActiveSheet()->getCell('F29')->getCalculatedValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F30')->getCalculatedValue());
        $this->assertEquals("City", $spreadsheet->getActiveSheet()->getCell('F31')->getCalculatedValue());
        $this->assertEquals("97901", $spreadsheet->getActiveSheet()->getCell('F32')->getCalculatedValue());

        $this->assertEquals("Akciová montáž dverí so zárubňou 35 € / kus. Akcia platí pri počte 6 a viac kusov. Minimálna suma na dvoch montážnikov je 210 €.", $spreadsheet->getActiveSheet()->getCell('A35')->getCalculatedValue());

        $this->assertEquals("12", $spreadsheet->getActiveSheet()->getCell('F38')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G38')->getValue());
        $this->assertEquals("12", $spreadsheet->getActiveSheet()->getCell('H38')->getCalculatedValue());

        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F41')->getValue());
        $this->assertEquals("5", $spreadsheet->getActiveSheet()->getCell('G41')->getValue());
        $this->assertEquals("5", $spreadsheet->getActiveSheet()->getCell('H41')->getCalculatedValue());

        $this->assertEquals("Aesthetic accessory 1", $spreadsheet->getActiveSheet()->getCell('A47')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F47')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G47')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('H47')->getCalculatedValue());

        $this->assertEquals("Aesthetic accessory 2", $spreadsheet->getActiveSheet()->getCell('A48')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('F48')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('G48')->getValue());
        $this->assertEquals("4", $spreadsheet->getActiveSheet()->getCell('H48')->getCalculatedValue());

        $this->assertEquals("Aesthetic accessory 3", $spreadsheet->getActiveSheet()->getCell('A49')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('F49')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G49')->getValue());
        $this->assertEquals("9", $spreadsheet->getActiveSheet()->getCell('H49')->getCalculatedValue());

        $this->assertEquals("10", $spreadsheet->getActiveSheet()->getCell('F53')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G53')->getValue());
        $this->assertEquals("30", $spreadsheet->getActiveSheet()->getCell('H53')->getCalculatedValue());

        $this->assertEquals(4.2, round(floatval($spreadsheet->getActiveSheet()->getCell('F54')->getCalculatedValue()), 2));
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G54')->getValue());
        $this->assertEquals(12.6, round(floatval($spreadsheet->getActiveSheet()->getCell('H54')->getCalculatedValue()), 2));

        $this->assertEquals("Aesthetic accessory 1", $spreadsheet->getActiveSheet()->getCell('A47')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F60')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G60')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('H60')->getCalculatedValue());

        $this->assertEquals("Aesthetic accessory 2", $spreadsheet->getActiveSheet()->getCell('A48')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('F61')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('G61')->getValue());
        $this->assertEquals("4", $spreadsheet->getActiveSheet()->getCell('H61')->getCalculatedValue());

        $this->assertEquals("Aesthetic accessory 3", $spreadsheet->getActiveSheet()->getCell('A49')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('F62')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G62')->getValue());
        $this->assertEquals("9", $spreadsheet->getActiveSheet()->getCell('H62')->getCalculatedValue());

        $this->assertEquals("Áno", $spreadsheet->getActiveSheet()->getCell('F65')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G65')->getValue());
        $this->assertEquals("128", $spreadsheet->getActiveSheet()->getCell('H65')->getCalculatedValue());

        $this->assertEquals("Áno", $spreadsheet->getActiveSheet()->getCell('F72')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G72')->getValue());
        $this->assertEquals("300", $spreadsheet->getActiveSheet()->getCell('H72')->getCalculatedValue());

        $this->assertEquals("Special surcharges 1", $spreadsheet->getActiveSheet()->getCell('A85')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('F85')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('G85')->getValue());
        $this->assertEquals("1", $spreadsheet->getActiveSheet()->getCell('H85')->getCalculatedValue());

        $this->assertEquals("Special surcharges 2", $spreadsheet->getActiveSheet()->getCell('A86')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('F86')->getValue());
        $this->assertEquals("2", $spreadsheet->getActiveSheet()->getCell('G86')->getValue());
        $this->assertEquals("4", $spreadsheet->getActiveSheet()->getCell('H86')->getCalculatedValue());

        $this->assertEquals("Special surcharges 3", $spreadsheet->getActiveSheet()->getCell('A87')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('F87')->getValue());
        $this->assertEquals("3", $spreadsheet->getActiveSheet()->getCell('G87')->getValue());
        $this->assertEquals("9", $spreadsheet->getActiveSheet()->getCell('H87')->getCalculatedValue());

        $this->assertEquals(2889.6, round(floatval($spreadsheet->getActiveSheet()->getCell('H89')->getCalculatedValue()), 2));
        $this->assertEquals(3554.21, round(floatval($spreadsheet->getActiveSheet()->getCell('H90')->getCalculatedValue()), 2));
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