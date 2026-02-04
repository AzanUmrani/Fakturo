<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'company_id',
        'billed_to_client_id',
        'invoice_id',
        'language_2_code',
        'total',
        'currency_3_code',
        'purpose',
        'made_by',
        'approved_by',
        'journal_number',
        'billing_regulation',
        'date',
    ];

    protected function billingRegulation(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => !empty($value) ? json_decode($value, true) : [],
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function billedToClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'billed_to_client_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getCurrencySymbol()
    {
        $currencyList = file_get_contents(__DIR__.'/../../resources/json/CurrencyList.json');
        $currencyList = json_decode($currencyList, true);

        return $currencyList[$this->currency_3_code] ?? ['symbol' => ''];
    }
}
