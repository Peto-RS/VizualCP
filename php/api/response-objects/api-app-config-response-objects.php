<?php

class AppConfigResponse
{
    /** @var string|null $baseUrl */
    public $baseUrl;

    /** @var boolean $reCaptchaEnabled */
    public $reCaptchaEnabled;

    /** @var string|null $reCaptchaSiteKey */
    public $reCaptchaSiteKey;

    public function __construct(?string $baseUrl, bool $reCaptchaEnabled, ?string $reCaptchaSiteKey)
    {
        $this->baseUrl = $baseUrl;
        $this->reCaptchaEnabled = $reCaptchaEnabled;
        $this->reCaptchaSiteKey = $reCaptchaSiteKey;
    }
}

?>
