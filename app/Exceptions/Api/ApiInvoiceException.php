<?php

namespace App\Exceptions\Api;

use App\Exceptions\ApiException;

class ApiInvoiceException extends ApiException
{
    const CODES = [
        'INVOICE_NOT_FOUND' => 'INVOICE_NOT_FOUND',
        'BILLED_CLIENT_NOT_FOUND' => 'BILLED_CLIENT_NOT_FOUND',
        'INVOICE_PDF_NOT_FOUND' => 'INVOICE_PDF_NOT_FOUND',
        'INVOICE_PDF_NOT_GENERATED' => 'INVOICE_PDF_NOT_GENERATED',
        'INVOICE_ZIP_NOT_GENERATED' => 'INVOICE_ZIP_NOT_GENERATED',
        'INVOICE_NUMBER_ALREADY_EXISTS' => 'INVOICE_NUMBER_ALREADY_EXISTS',
    ];

    public static function invoiceNotFound(): self
    {
        return new self(self::CODES['INVOICE_NOT_FOUND'], 404);
    }

    public static function billedClientNotFound(): self
    {
        return new self(self::CODES['BILLED_CLIENT_NOT_FOUND'], 404);
    }

    public static function invoicePdfNotFound(string $type): self
    {
        return new self(self::CODES['INVOICE_PDF_NOT_FOUND'], 404);
    }

    public static function invoicePdfNotGenerated(): self
    {
        return new self(self::CODES['INVOICE_PDF_NOT_GENERATED'], 404);
    }

    public static function invoiceZipNotGenerated(): self
    {
        return new self(self::CODES['INVOICE_ZIP_NOT_GENERATED'], 500);
    }

    public static function invoiceNumberAlreadyExists(): self
    {
        return new self(self::CODES['INVOICE_NUMBER_ALREADY_EXISTS'], 400);
    }
}
