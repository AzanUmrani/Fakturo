<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\AutocompleteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutocompleteController extends Controller
{
    public function getCompanyDataFromExternalSource(Request $request): JsonResponse
    {
        return AutocompleteService::getCompanyDataFromExternalSource($request->get('country2code') ?? '', $request->get('registrationNumber') ?? '');
    }
}
