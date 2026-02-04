<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $userResponse = null;
        if (isset($this->resource['user'])) {
            $userResponse = [
                'id' => $this->resource['user']->id,
                'uuid' => $this->resource['user']->uuid,
                'email' => $this->resource['user']->email,
                'apple_token' => $this->resource['user']->apple_token,
                'apple_extra' => $this->resource['user']->apple_extra,
                'google_token' => $this->resource['user']->google_token,
                'google_extra' => $this->resource['user']->google_extra,
                'facebook_token' => $this->resource['user']->facebook_token,
                'facebook_extra' => $this->resource['user']->facebook_extra,
                'invoice_count' => $this->resource['user']->invoice_count,
            ];
        }

        if (isset($this->resource['type'])) {
            $userResponse['type'] = $this->resource['type'];
        }

        return [
            'token' => $this->resource['token'] ?? null,
            'user' => $userResponse,
        ];
    }
}
