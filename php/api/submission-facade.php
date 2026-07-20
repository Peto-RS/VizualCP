<?php

require_once __DIR__ . '/model/validation-message.php';
require_once __DIR__ . '/json-data-manipulation-facade.php';
require_once __DIR__ . '/price-offer-spreadsheet-facade.php';
require_once __DIR__ . '/../enums/SupportedLanguagesEnum.php';

/**
 * @return ValidationMessage[]
 */
function submitPriceOffer(array $requestBody, string $lang): array
{
    if (!AppConfigJsonDataManipulation::getAll()['reCaptchaEnabled'] || verifyRecaptcha($requestBody['g-recaptcha-response'])) {
        try {
            $priceOffer = PriceOffer::fromSession($_SESSION['priceOffer']);
            $priceOfferResponse = $priceOffer->toResponse($priceOffer->currency);
            $spreadsheet = generateSpreadSheet($priceOfferResponse, $lang);

            sendMailWithExcelAttachment(writeSpreadSheetToOutput($spreadsheet), $priceOfferResponse, $lang);
            return array();
        } catch (Throwable $e) {
            error_log($e);
            return array(new ValidationMessage('ERROR_SENDING_MAIL', array()));
        }
    } else {
        return array(new ValidationMessage('RECAPTCHA_FAILED', array()));
    }
}

function verifyRecaptcha($token): bool
{
    $secretKey = AppConfigJsonDataManipulation::getAll()['reCaptchaSecretKey'];
    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$token"
    );
    $result = json_decode($verify);
    return boolval($result->success);
}