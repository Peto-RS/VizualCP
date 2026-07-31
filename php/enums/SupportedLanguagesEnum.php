<?php

abstract class SupportedLanguagesEnum
{
    const BG = "bg";
    const CS = "cs";
    const DE = "de";
    const EN = "en";
    const ES = "es";
    const FR = "fr";
    const HU = "hu";
    const IT = "it";
    const PL = "pl";
    const RO = "ro";
    const SK = "sk";
    const SR = "sr";

    public static function getAll(): array
    {
        return [
            self::BG,
            self::CS,
            self::DE,
            self::EN,
            self::ES,
            self::FR,
            self::HU,
            self::IT,
            self::PL,
            self::RO,
            self::SK,
            self::SR
        ];
    }

    public static function isValid(?string $lang): bool
    {
        return in_array($lang, SupportedLanguagesEnum::getAll(), true);
    }
}