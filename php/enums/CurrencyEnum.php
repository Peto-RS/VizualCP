<?php

abstract class CurrencyEnum
{
    const CZK = "CZK";
    const EUR = "EUR";

    public static function isValid(?string $currency): bool
    {
        return in_array($currency, [self::CZK, self::EUR], true);
    }
}