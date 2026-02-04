<?php

namespace App\Exceptions\Api;

use App\Exceptions\ApiException;

class ApiClientException extends ApiException
{
    const CODES = [
        'CLIENT_NOT_FOUND' => 'CLIENT_NOT_FOUND',
        'CLIENT_HAS_NO_INVOICES' => 'CLIENT_HAS_NO_INVOICES',
        'CLIENT_STATEMENT_PDF_NOT_GENERATED' => 'CLIENT_STATEMENT_PDF_NOT_GENERATED',
    ];

    public static function clientNotFound(): self
    {
        return new self(self::CODES['CLIENT_NOT_FOUND'], 404);
    }

    public static function clientHasNoInvoices(): self
    {
        return new self(self::CODES['CLIENT_HAS_NO_INVOICES'], 404);
    }

    public static function clientStatementPdfNotGenerated(): self
    {
        return new self(self::CODES['CLIENT_STATEMENT_PDF_NOT_GENERATED'], 500);
    }
}
