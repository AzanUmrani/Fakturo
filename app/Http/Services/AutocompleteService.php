<?php

namespace App\Http\Services;

use App\Models\CompanyInfo;
use Illuminate\Http\JsonResponse;
use Foxentry\ApiClient;

class AutocompleteService
{
    //https://foxentry.dev/reference/datascopes

    private static function getApiClient(): ApiClient
    {
        return new ApiClient(env('FOXENTRY_API_KEY'));
    }

    public static function getCompanyDataFromExternalSource(string $country2Code, string $registrationNumber): JsonResponse
    {
        if (!$country2Code || !$registrationNumber) {
            return response()->json([
                'error' => 'Invalid input',
            ], 400);
        }

        /* check if we already have that */
        $companyInfo = CompanyInfo::where('country_2_code', $country2Code)
            ->where('registration_number', $registrationNumber)
            ->first();

        $cached = (bool)$companyInfo;
        if (!$companyInfo) {
            try {
                $apiClient = self::getApiClient();
                $response = $apiClient
                    ->company()
                    ->setOptions([
                        'dataScope' => 'extended',
                        'resultsLimit' => 1,
                    ])
                    ->search([
                        'type' => 'registrationNumber',
                        'filter' => [
                            'country' => $country2Code,
                        ],
                        'value' => $registrationNumber, // searched value
                    ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }

            $response = $response->getResponse();
            if (!$response) {
                return response()->json([
                    'error' => 'No data found',
                ], 404);
            }

            $response = $response?->results[0]?->data ?? null;
            $responseRegistration = $response?->registrations[0]?->data ?? null;

            if ($response) {
                $companyInfo = new CompanyInfo();
                $companyInfo->country_2_code = $country2Code;
                $companyInfo->registration_number = $registrationNumber;
                $companyInfo->external_response = json_encode($response);
                $companyInfo->save();
            }
        } else {
            $response = json_decode($companyInfo->external_response);
            $responseRegistration = $response?->registrations[0]?->data ?? null;
        }


        $returnData = [
            'cached' => $cached,

            'country' => $response?->country ?? '',
            'name' => $response?->name ?? '',

            'registrationNumber' => $response?->registrationNumber ?? '',
            'taxNumber' => $response?->taxNumber ?? '',
            'vatNumber' => $response?->vatNumber ?? '',
            'vatPayer' => $response?->vat?->status ? !($response?->vat?->status === 'nonpayer') : null,

            'address' => $response?->addressOfficial?->data?->streetWithNumber ?? '',
            'city' => $response?->addressOfficial?->data?->city ?? '',
            'zip' => $response?->addressOfficial?->data?->zip ?? '',

            'registration' => ($responseRegistration?->registrator?->name ?? '').($responseRegistration?->reference ? ', '.$responseRegistration?->reference : ''),
        ];

        return response()->json(['data' => $returnData]);
    }
}
