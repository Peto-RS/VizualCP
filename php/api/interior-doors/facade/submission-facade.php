<?php

require_once __DIR__ . '/../../common/validation-message.php';
require_once __DIR__ . '/../../common/facade/db-facade.php';
require_once __DIR__ . '/../../common/facade/recaptcha-facade.php';
require_once __DIR__ . '/campaign-facade.php';
require_once __DIR__ . '/../model/cart-model.php';
require_once __DIR__ . '/../../common/facade/json-data-manipulation-facade.php';
require_once __DIR__ . '/price-offer-mail-facade.php';
require_once __DIR__ . '/price-offer-spreadsheet-facade.php';
require_once __DIR__ . '/../../../enums/AuditActionEnum.php';
require_once __DIR__ . '/../../../enums/AuditResourceTypeEnum.php';
require_once __DIR__ . '/../../../enums/ErrorCodeEnum.php';
require_once __DIR__ . '/../../../enums/SupportedLanguagesEnum.php';

/**
 * @return ValidationMessage[]
 */
function submitPriceOffer(array $requestBody, string $lang): array
{
    $route = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: null;

    createAuditLogRecord(
        getDbConnection(),
        $_SESSION['session_ref'] ?? null,
        'anonymous',
        AuditActionEnum::SUBMISSION_START_INTERIOR_DOORS,
        AuditResourceTypeEnum::PRICE_OFFER,
        null,
        $route
    );

    if (!AppConfigJsonDataManipulation::getAll()['reCaptchaEnabled'] || verifyRecaptcha($requestBody['g-recaptcha-response'])) {
        try {
            $priceOffer = PriceOffer::fromSession($_SESSION['priceOffer']);
            $priceOfferResponse = $priceOffer->toResponse(
                $priceOffer->currency,
                getActiveCampaignsOfType(CampaignTypeEnum::DOORS, new DateTime(), AppConfigJsonDataManipulation::getAll()["campaignDisabled"] ? array() : CampaignsJsonDataManipulation::getAll())
            );
        } catch (Throwable $e) {
            error_log($e);
            createErrorLogRecord(
                getDbConnection(),
                $_SESSION['session_ref'] ?? null,
                ErrorCodeEnum::INTERIOR_DOORS_PRICE_OFFER_PREPARATION_FAILED,
                $e->getMessage(),
                'submission',
                $e->__toString(),
                $route
            );
            return array(new ValidationMessage('ERROR_SENDING_MAIL', array()));
        }

        try {
            $spreadsheet = generateSpreadSheet($priceOfferResponse, $lang);
            $spreadsheetOutput = writeSpreadSheetToOutput($spreadsheet);
        } catch (Throwable $e) {
            error_log($e);
            createErrorLogRecord(
                getDbConnection(),
                $_SESSION['session_ref'] ?? null,
                ErrorCodeEnum::INTERIOR_DOORS_SPREADSHEET_GENERATION_FAILED,
                $e->getMessage(),
                'submission',
                $e->__toString(),
                $route
            );
            return array(new ValidationMessage('ERROR_SENDING_MAIL', array()));
        }

        try {
            $email = preparePriceOfferEmail($spreadsheetOutput, $priceOfferResponse, $lang);
        } catch (Throwable $e) {
            error_log($e);
            createErrorLogRecord(
                getDbConnection(),
                $_SESSION['session_ref'] ?? null,
                ErrorCodeEnum::INTERIOR_DOORS_EMAIL_PREPARATION_FAILED,
                $e->getMessage(),
                'mailer',
                $e->__toString(),
                $route
            );
            return array(new ValidationMessage('ERROR_SENDING_MAIL', array()));
        }

        try {
            sendPreparedEmail($email);
        } catch (Throwable $e) {
            error_log($e);
            createErrorLogRecord(
                getDbConnection(),
                $_SESSION['session_ref'] ?? null,
                ErrorCodeEnum::INTERIOR_DOORS_MAIL_SEND_FAILED,
                $e->getMessage(),
                'mailer',
                $e->__toString(),
                $route
            );
            return array(new ValidationMessage('ERROR_SENDING_MAIL', array()));
        }

        createAuditLogRecord(
            getDbConnection(),
            $_SESSION['session_ref'] ?? null,
            'anonymous',
            AuditActionEnum::SUBMISSION_PASSED_INTERIOR_DOORS,
            AuditResourceTypeEnum::PRICE_OFFER,
            null,
            $route
        );

        return array();
    } else {
        return array(new ValidationMessage('RECAPTCHA_FAILED', array()));
    }
}