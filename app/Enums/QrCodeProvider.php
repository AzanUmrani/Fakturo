<?php

namespace App\Enums;

enum QrCodeProvider: string
{
    case PAY_BY_SQUARE = 'PAY_BY_SQUARE';
    case UNIVERSAL = 'UNIVERSAL';

    public static function toArray(): array
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return array_values($oClass->getConstants());
    }

    public static function toArrayStrings(): array
    {
        $oClass = new \ReflectionClass(__CLASS__);
        $constants = $oClass->getConstants();
        $values = [];
        foreach ($constants as $constant) {
            $values[] = $constant->value;
        }
        return $values;
    }
}
