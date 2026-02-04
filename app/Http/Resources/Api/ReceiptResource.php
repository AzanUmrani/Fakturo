<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\CompanyResource;
use App\Http\Resources\Api\ClientResource;

class ReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'company' => new CompanyResource($this->company),
            'billed_to_client' => new ClientResource($this->billedToClient),
            'language_2_code' => $this->language_2_code,
            'total' => $this->total,
            'currency_3_code' => $this->currency_3_code,
            'currency_symbol' => $this->getCurrencySymbol(),
            'purpose' => $this->purpose,
            'made_by' => $this->made_by,
            'approved_by' => $this->approved_by,
            'journal_number' => $this->journal_number,
            'billing_regulation' => $this->billing_regulation,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
