<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PreInvoiceCollectionResource extends ResourceCollection
{
    /**
     * @return array<int|string, mixed>
     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
