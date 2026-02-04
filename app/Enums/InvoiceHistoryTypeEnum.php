<?php

namespace App\Enums;

enum InvoiceHistoryTypeEnum: string
{
    /* !!! if new value is added/modified, reflect changes in ``invoice_histories`` table -> manual !!! */
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';
    case Sent = 'sent';
    case Paid = 'paid';
    case Unpaid = 'unpaid';
    case Regenerated = 'regenerated';
    /* !!! if new value is added/modified, reflect changes in ``invoice_histories`` table -> manual !!! */

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
