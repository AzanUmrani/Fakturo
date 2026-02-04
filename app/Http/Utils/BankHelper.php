<?php

namespace App\Http\Utils;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Isdoc\Enums\DocumentType;
use Isdoc\Enums\PaymentMeansCode;
use Isdoc\Enums\TaxScheme;
use Isdoc\Models\BankAccount;
use Isdoc\Models\ClassifiedTaxCategory;
use Isdoc\Models\Country;
use Isdoc\Models\InvoiceLine;
use Isdoc\Models\Item;
use Isdoc\Models\PartyContact;
use Isdoc\Models\PartyIdentification;
use Isdoc\Models\Payment;
use Isdoc\Models\PaymentDetailBankTransaction;
use Isdoc\Models\PaymentDetailCash;
use Isdoc\Models\PaymentDetails;
use Isdoc\Models\PaymentMeans;
use Isdoc\Models\PostalAddress;
use Ramsey\Uuid\Uuid;
use setasign\Fpdi\Tcpdf\Fpdi;

class BankHelper {
    public static function ibanToNationalAccount($iban, $bankCode = null)
    {
        $iban = strtoupper(str_replace(' ', '', $iban));
        $country = substr($iban, 0, 2);

        switch ($country) {
            case 'SK': // Slovensko
                // SK BBAN má 20 číslic
                $bban = substr($iban, 4, 20);

                $detectedBankCode = substr($bban, 0, 4);
                $prefix = ltrim(substr($bban, 4, 6), '0');
                $account = ltrim(substr($bban, 10, 10), '0');

                // preferuj externý kód banky, ak je zadaný
                $bankCode = $bankCode ?: $detectedBankCode;

                if ($prefix !== '') {
                    return "{$prefix}-{$account}/{$bankCode}";
                }
                return "{$account}/{$bankCode}";

            case 'CZ': // Česko
                // CZ BBAN = 20 numerických znakov presne ako SK
                $bban = substr($iban, 4, 20);

                $detectedBankCode = substr($bban, 0, 4);
                $prefix = ltrim(substr($bban, 4, 6), '0');
                $account = ltrim(substr($bban, 10, 10), '0');

                $bankCode = $bankCode ?: $detectedBankCode;

                if ($prefix !== '') {
                    return "{$prefix}-{$account}/{$bankCode}";
                }
                return "{$account}/{$bankCode}";
        }

        return '';
    }
}
