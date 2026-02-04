<?php

namespace App\Models;

use App\Enums\QrCodeProvider;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'default',
        'name',

        'state',
        'street',
        'street_extra',
        'street_extra',
        'zip',
        'city',

        'tax_type',
        'identification_number', // ico
        'vat_identification_number', // dic
        'vat_identification_number_sk', // icdph

        'registry_info',

        'contact_name',
        'contact_phone',
        'contact_email',
        'contact_web',

        'payment_methods',
        'template',
    ];

    protected function paymentMethods(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $value ? json_decode($value, true) : [],
        );
    }

    protected function template(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $value ? json_decode($value, true) : [],
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function preInvoices(): HasMany
    {
        return $this->hasMany(PreInvoice::class);
    }

    public function getResourcesPath(): string
    {
        return 'user/'.$this->user_id.'/company_'.$this->id;
    }

    public function getSignaturePath(): string
    {
        return $this->getResourcesPath().'/signature.png';
    }

    public function getCurrencyData()
    {
        $currencyList = file_get_contents(__DIR__ . '/../../resources/json/CurrencyList.json'); // TODO change
        $currencyList = json_decode($currencyList, true);

        return $currencyList[$this->template['currency']];
    }

    public static function getDefaultTemplate(): array
    {
        return [
            'invoice' => [
                'template' => 'Kronos',
                'primary_color' => '#b23a5a', /* TODO */
                'currency' => 'EUR', /* TODO */
                'language' => 'SK', /* TODO */
                'numbering' => [
                    'prefix' => '',
                    'upcoming' => 1, /* TODO */
                    'format' => 'YEAR:4;NUMBER:3', /* TODO */
                    'due_date_additional_days' => 14, /* TODO */
                ],
                'formats' => [
                    'date' => 'd.m.Y', /* TODO */
                    'decimal' => '.', /* TODO */
                    'thousands' => ',', /* TODO */
                ],
                'visibility' => [
                    'due_date' => true, /* TODO */
                    'send_date' => true, /* TODO */
                    'quantity' => true, /* TODO */
                    'payment' => true, /* TODO */
                    'qr_payment' => true, /* TODO */
                ],
                'qr' => [
                    'provider' => QrCodeProvider::UNIVERSAL,
                ],
            ],
            'preInvoice' => [
                'template' => 'Kronos',
                'primary_color' => '#b23a5a', /* TODO */
                'currency' => 'EUR', /* TODO */
                'language' => 'SK', /* TODO */
                'numbering' => [
                    'prefix' => '',
                    'upcoming' => 1, /* TODO */
                    'format' => 'YEAR:4;NUMBER:3', /* TODO */
                    'due_date_additional_days' => 14, /* TODO */
                ],
                'formats' => [
                    'date' => 'd.m.Y', /* TODO */
                    'decimal' => '.', /* TODO */
                    'thousands' => ',', /* TODO */
                ],
                'visibility' => [
                    'due_date' => true, /* TODO */
                    'send_date' => true, /* TODO */
                    'quantity' => true, /* TODO */
                    'payment' => true, /* TODO */
                    'qr_payment' => true, /* TODO */
                ],
                'qr' => [
                    'provider' => QrCodeProvider::UNIVERSAL,
                ],
            ]
        ];
    }
}
