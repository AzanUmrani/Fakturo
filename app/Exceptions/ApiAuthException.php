<?php

namespace App\Exceptions;

use Exception;

class ApiAuthException extends ApiException
{
    const CODES = [
        'NOT_AUTHORIZED' => 'NOT_AUTHORIZED',
        'BAD_CREDENTIALS' => 'BAD_CREDENTIALS',
        'SOCIAL_TOKEN_ALREADY_EXISTS' => 'SOCIAL_TOKEN_ALREADY_EXISTS',
    ];

    public static function notAuthorized(): self
    {
        return new self(self::CODES['NOT_AUTHORIZED'], 401);
    }

    public static function badCredentials(): self
    {
        return new self(self::CODES['BAD_CREDENTIALS'], 400);
    }

    public static function socialTokenAlreadyAssigned(): self
    {
        return new self(self::CODES['SOCIAL_TOKEN_ALREADY_EXISTS'], 400);
    }
}
