<?php

require_once __DIR__ . '/json-data-manipulation-facade.php';

function verifyRecaptcha($token): bool
{
    $secretKey = AppConfigJsonDataManipulation::getAll()['reCaptchaSecretKey'];
    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$token"
    );
    $result = json_decode($verify);
    return boolval($result->success);
}
