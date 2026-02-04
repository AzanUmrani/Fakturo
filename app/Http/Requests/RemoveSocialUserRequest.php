<?php

namespace App\Http\Requests;

use App\Http\Services\SocialAuthService;
use Illuminate\Foundation\Http\FormRequest;

class RemoveSocialUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $in = 'in:' . implode(',', array_keys(SocialAuthService::SOCIAL_PROVIDERS));

        return [
            'provider' => ['required', 'string', $in],
        ];
    }
}
