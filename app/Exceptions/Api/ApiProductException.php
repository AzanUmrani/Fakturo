<?php

namespace App\Exceptions\Api;

use App\Exceptions\ApiException;

class ApiProductException extends ApiException
{
    const CODES = [
        'PRODUCT_NOT_FOUND' => 'PRODUCT_NOT_FOUND',
    ];

    public static function notFound(): self
    {
        return new self(self::CODES['PRODUCT_NOT_FOUND'], 404);
    }
}
