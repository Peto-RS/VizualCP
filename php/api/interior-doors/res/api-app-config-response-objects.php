<?php

class AppConfigResponse
{
    /** @var string|null $baseUrl */
    public $baseUrl;

    /** @var boolean $profile */
    public $profile;

    /** @var boolean $reCaptchaEnabled */
    public $reCaptchaEnabled;

    /** @var string|null $reCaptchaSiteKey */
    public $reCaptchaSiteKey;

    public function __construct(
        ?string $baseUrl,
        string  $profile,
        bool    $reCaptchaEnabled,
        ?string $reCaptchaSiteKey
    )
    {
        $this->baseUrl = $baseUrl;
        $this->profile = $profile;
        $this->reCaptchaEnabled = $reCaptchaEnabled;
        $this->reCaptchaSiteKey = $reCaptchaSiteKey;
    }
}
