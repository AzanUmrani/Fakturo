<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $returnData = [
            'id' => $this->resource['id'],
            'uuid' => $this->resource['uuid'],

            'name' => $this->resource['name'],

            'state' => $this->resource['state'],
            'street' => $this->resource['street'],
            'street_extra' => $this->resource['street_extra'],
            'zip' => $this->resource['zip'],
            'city' => $this->resource['city'],

            'identification_number' => $this->resource['identification_number'],
            'vat_identification_number' => $this->resource['vat_identification_number'],
            'vat_identification_number_sk' => $this->resource['vat_identification_number_sk'],

            'registry_info' => $this->resource['registry_info'],

            'contact_name' => $this->resource['contact_name'],
            'contact_phone' => $this->resource['contact_phone'],
            'contact_email' => $this->resource['contact_email'],
            'contact_web' => $this->resource['contact_web'],
        ];

        if (!empty($this->resource['invoicesMetaData'])) {
            $returnData['invoicesMetaData'] = $this->resource['invoicesMetaData'];
        }

        return $returnData;
    }

}
