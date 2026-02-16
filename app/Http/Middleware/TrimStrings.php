<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',

        'formats.decimal', // CompanySetTemplateRequest.php
        'formats.thousands', // CompanySetTemplateRequest.php
        'preInvoice.formats.decimal', // CompanySetTemplateRequest.php
        'preInvoice.formats.thousands', // CompanySetTemplateRequest.php
    ];
}
