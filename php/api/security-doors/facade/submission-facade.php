<?php

require_once __DIR__ . '/../../common/validation-message.php';
require_once __DIR__ . '/../../common/facade/db-facade.php';
require_once __DIR__ . '/../../common/facade/json-data-manipulation-facade.php';
require_once __DIR__ . '/../../common/facade/recaptcha-facade.php';
require_once __DIR__ . '/../model/model-security-doors.php';
require_once __DIR__ . '/price-offer-mail-facade.php';
require_once __DIR__ . '/price-offer-spreadsheet-facade.php';
require_once __DIR__ . '/../../../enums/ErrorCodeEnum.php';
require_once __DIR__ . '/../../../enums/SupportedLanguagesEnum.php';

/**
 * @return ValidationMessage[]
 */
function submitPriceOfferSecurityDoors(array $requestBody, string $lang): array
{
    $route = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: null;

    if (!AppConfigJsonDataManipulation::getAll()['reCaptchaEnabled'] || verifyRecaptcha($requestBody['g-recaptcha-response'])) {
        try {
            $priceOffer = SecurityDoorsPriceOffer::fromSession($_SESSION['securityDoorsPriceOffer']);
            $priceOfferResponse = $priceOffer->toResponse(
                $priceOffer->currency
            );
        } catch (Throwable $e) {
            error_log($e);
            createErrorLogRecord(
                getDbConnection(),
                $_SESSION['session_ref'] ?? null,
                ErrorCodeEnum::SECURITY_DOORS_PRICE_OFFER_PREPARATION_FAILED,
                $e->getMessage(),
                'submission',
                $e->__toString(),
                $route
            );
            return array(new ValidationMessage('ERROR_SENDING_MAIL', array()));
        }

        try {
            $spreadsheet = generateSpreadSheetSecurityDoors($priceOfferResponse, $lang);
            $spreadsheetOutput = writeSpreadSheetToOutputSecurityDoors($spreadsheet);
        } catch (Throwable $e) {
            error_log($e);
            createErrorLogRecord(
                getDbConnection(),
                $_SESSION['session_ref'] ?? null,
                ErrorCodeEnum::SECURITY_DOORS_SPREADSHEET_GENERATION_FAILED,
                $e->getMessage(),
                'submission',
                $e->__toString(),
                $route
            );
            return array(new ValidationMessage('ERROR_SENDING_MAIL', array()));
        }

        try {
            $email = prepareSecurityDoorsPriceOfferEmail($spreadsheetOutput, $priceOfferResponse, $lang);
        } catch (Throwable $e) {
            error_log($e);
            createErrorLogRecord(
                getDbConnection(),
                $_SESSION['session_ref'] ?? null,
                ErrorCodeEnum::SECURITY_DOORS_EMAIL_PREPARATION_FAILED,
                $e->getMessage(),
                'mailer',
                $e->__toString(),
                $route
            );
            return array(new ValidationMessage('ERROR_SENDING_MAIL', array()));
        }

        try {
            sendPreparedSecurityDoorsEmail($email);
        } catch (Throwable $e) {
            error_log($e);
            createErrorLogRecord(
                getDbConnection(),
                $_SESSION['session_ref'] ?? null,
                ErrorCodeEnum::SECURITY_DOORS_MAIL_SEND_FAILED,
                $e->getMessage(),
                'mailer',
                $e->__toString(),
                $route
            );
            return array(new ValidationMessage('ERROR_SENDING_MAIL', array()));
        }

        return array();
    } else {
        return array(new ValidationMessage('RECAPTCHA_FAILED', array()));
    }
}