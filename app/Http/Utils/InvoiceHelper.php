<?php

namespace App\Http\Utils;

class InvoiceHelper
{

    const INVOICE_NUMBER_FORMAT_LIST = [
        'YEAR:4;NUMBER:4',
        'YEAR:4;NUMBER:3',
        'NUMBER:3;YEAR:4',
        'NUMBER:4;YEAR:4',
    ];

    public static function getInvoiceNumberWithoutYear(string $invocieNumber, string $invoiceFormat): int
    {
        $formatIndex = array_search($invoiceFormat, self::INVOICE_NUMBER_FORMAT_LIST);

        if ($formatIndex === 0) {
            return intval(substr($invocieNumber, 4));
        }

        if ($formatIndex === 1) {
            return intval(substr($invocieNumber, 5));
        }

        if ($formatIndex === 2) {
            return intval(substr($invocieNumber, 0, 3));
        }

        if ($formatIndex === 3) {
            return intval(substr($invocieNumber, 0, 4));
        }

        /* DEFAULT */
        return intval(substr($invocieNumber, 4));
    }

}
