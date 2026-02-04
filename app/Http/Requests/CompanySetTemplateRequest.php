<?php

namespace App\Http\Requests;

use App\Enums\DocumentTemplateName;
use App\Enums\QrCodeProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CompanySetTemplateRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        $templateNameList = DocumentTemplateName::toArrayStrings();
        $qrCodeProviderList = QrCodeProvider::toArrayStrings();

        return [
            /* INVOICE */ /* TODO add invoice prefix */
            'template' => 'required|string|in:'.implode(',', $templateNameList),
            'primary_color' => 'required|string',
            'currency' => 'required|string|max:3|min:3',
            'language' => 'required|string|max:2|min:2',

            'numbering.upcoming' => 'required|int',
            'numbering.format' => 'required|string',
            'numbering.due_date_additional_days' => 'required|int',

            'formats.date' => 'required|string',
            'formats.decimal' => 'string',
            'formats.thousands' => 'string',

            'visibility.send_date' => 'boolean', /* TODO add require when apple approve old version ... */
            'visibility.due_date' => 'required|boolean',
            'visibility.quantity' => 'required|boolean',
            'visibility.payment' => 'required|boolean',
            'visibility.qr_payment' => 'required|boolean',

            'qr.provider' => 'string|in:'.implode(',', $qrCodeProviderList),

            /* PREINVOICE */ /* TODO add missing required fields */
            'preInvoice.template' => 'string|in:'.implode(',', $templateNameList),
            'preInvoice.primary_color' => 'string',
            'preInvoice.currency' => 'string|max:3|min:3',
            'preInvoice.language' => 'string|max:2|min:2',

            'preInvoice.numbering.upcoming' => 'int',
            'preInvoice.numbering.format' => 'string',
            'preInvoice.numbering.due_date_additional_days' => 'int',

            'preInvoice.formats.date' => 'string',
            'preInvoice.formats.decimal' => 'string',
//            'preInvoice.formats.thousands' => 'string',
            'preInvoice.formats.thousands' => '',

            'preInvoice.visibility.send_date' => 'boolean', /* TODO add require when apple approve old version ... */
            'preInvoice.visibility.due_date' => 'boolean',
            'preInvoice.visibility.quantity' => 'boolean',
            'preInvoice.visibility.payment' => 'boolean',
            'preInvoice.visibility.qr_payment' => 'boolean',

            'preInvoice.qr.provider' => 'string|in:'.implode(',', $qrCodeProviderList),
        ];
    }
}
