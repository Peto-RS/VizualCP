<?php

abstract class DoorsWidthEnum
{
    const W60 = "W60";
    const W70 = "W70";
    const W80 = "W80";
    const W90 = "W90";

    public static function getWidthString($width): string
    {
        switch ($width) {
            case DoorsWidthEnum::W60:
                return "60";
            case DoorsWidthEnum::W70:
                return "70";
            case DoorsWidthEnum::W80:
                return "80";
            case DoorsWidthEnum::W90:
                return "90";
            default:
                return "";
        }
    }

    public static function getFee($width): int
    {
        switch ($width) {
            case DoorsWidthEnum::W70:
                return 3;
            case DoorsWidthEnum::W80:
                return 6;
            case DoorsWidthEnum::W90:
                return 9;
            default:
                return 0;
        }
    }
}