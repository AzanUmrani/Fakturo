<?php

namespace App\Enums;

enum DocumentTemplateName: string
{
//    const DEFAULT = 'Sarif'; // TODO CHANGE TO Sango, CHANGE DB default from Sarif to Sango, PROBLEM cuz getConstants

    case Sarif = 'Sarif'; // TODO DELETE,
    case Sango = 'Sango';

    case Kros = 'Kros'; // TODO DELETE
    case Kronos = 'Kronos';

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
