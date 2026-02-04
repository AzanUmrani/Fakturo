<?php

namespace App\Exceptions\Api;

use App\Exceptions\ApiException;

class ApiCompanyException extends ApiException
{

    const CODES = [
        'COMPANY_NOT_FOUND' => 'COMPANY_NOT_FOUND',
        'COMPANY_SIGNATURE_NOT_UPDATED' => 'COMPANY_SIGNATURE_NOT_UPDATED',
        'COMPANY_SIGNATURE_NOT_FOUND' => 'COMPANY_SIGNATURE_NOT_FOUND',
        'NO_CLIENT_FOUND' => 'NO_CLIENT_FOUND',
    ];

    public static function companyNotFound(): self
    {
        return new self(self::CODES['COMPANY_NOT_FOUND'], 404);
    }

    public static function signatureNotUpdated(): self
    {
        return new self(self::CODES['COMPANY_SIGNATURE_NOT_UPDATED'], 500);
    }

    public static function signatureNotFound(): self
    {
        return new self(self::CODES['COMPANY_SIGNATURE_NOT_FOUND'], 404);
    }

    public static function noClientFound(): self
    {
        return new self(self::CODES['NO_CLIENT_FOUND'], 400);
    }
}
