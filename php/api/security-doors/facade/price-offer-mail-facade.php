<?php

require_once __DIR__ . '/../../common/facade/db-facade.php';
require_once __DIR__ . '/../../common/facade/json-data-manipulation-facade.php';
require_once __DIR__ . '/../../../model/utils/translation-utils.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../../../../vendor/autoload.php';

function prepareSecurityDoorsPriceOfferEmail(
    string $excelSpreadsheet,
    SecurityDoorsPriceOfferResponse $priceOffer,
    string $lang
): PHPMailer
{
    $appConfig = AppConfigJsonDataManipulation::getAll();
    $email = $appConfig["isProductionSmtp"] ? getMailConfigProd($appConfig) : getMailConfigLocalhost();

    $email->isHTML();
    $email->setFrom($appConfig["mailFrom"], $appConfig["mailFromName"]);
    $email->addCC($appConfig["mailCc"]);

    $mailAddressTo = $priceOffer->contact->email;
    $email->addAddress($mailAddressTo);

    $email->addEmbeddedImage(
        __DIR__ . '/../../../../assets/img/mail.jpg',
        'mail_image',
        'logo.jpg',
        'base64',
        'image/jpeg'
    );

    $priceOfferSequentialId = str_pad(
        incrementAndGetPriceOfferNumber(getDbConnection(), 'stolarstvo-sucansky', 'security'),
        3,
        '0',
        STR_PAD_LEFT
    );
    $priceOfferNumber = date('y-m') . "-" . $priceOfferSequentialId;
    $email->Subject = '💥 STOLÁRSTVO SUČANSKÝ 💥 - CENOVÁ PONUKA - 🚪 BEZPEČNOSTNÉ DVERE 🚪 ČÍSLO - ' . $priceOfferNumber;

    $isForeignLanguage = $lang !== "sk";
    $translationFileSk = getArrayFromJsonFile(__DIR__ . '/../../../../vue-project/src/locales/' . SupportedLanguagesEnum::SK . '.json');
    $translationFileForeign = getArrayFromJsonFile(__DIR__ . '/../../../../vue-project/src/locales/' . $lang . '.json');

    $email->Body = replaceTranslationParams(
        file_get_contents(__DIR__ . '/../../../resource/price-offer-mail.html'),
        array(
            'content_foreign_language' => $isForeignLanguage ? $translationFileForeign['priceOfferMail'] : '',
            'content_sk' => $translationFileSk['priceOfferMail']
        )
    );

    $email->addStringAttachment(
        $excelSpreadsheet,
        "cenova_ponuka_$priceOfferNumber.xlsx",
        'base64',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );

    return $email;
}

/**
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendPreparedSecurityDoorsEmail(PHPMailer $email): void
{
    $email->send();
}

function getMailConfigLocalhost(): PHPMailer
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->CharSet = "UTF-8";
    $mail->Host = '127.0.0.1';
    $mail->Port = 25;
    $mail->SMTPAuth = false;
    $mail->SMTPSecure = false;

    return $mail;
}

function getMailConfigProd(array $appConfig): PHPMailer
{
    $mail = new PHPMailer(true);

    if ($appConfig["mailSmtpDebugMode"]) {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    }

    $mail->isSMTP();
    $mail->CharSet = "UTF-8";
    $mail->Host = $appConfig["mailSmtpHost"];
    $mail->SMTPAuth = true;
    $mail->Username = $appConfig["mailSmtpUsername"];
    $mail->Password = $appConfig["mailSmtpPassword"];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    return $mail;
}