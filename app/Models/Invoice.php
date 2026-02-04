<?php

namespace App\Models;

use App\Http\Utils\FileHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'uuid',
        'company_id',

        'prefix',
        'number',
        'billed_date',
        'due_date',
        'send_date',

        'variable_symbol',
        'constant_symbol',
        'specific_symbol',

        'order_id',

        'billed_from_client',
        'billed_to_client',
        'items',

        'payment',
        'bank_transfer',

        'note',

        'totalPrice',
        'totalPrice_with_tax',
        'totalPrice_tax',
        'cash_payment_rounding',
        'tax_data',

        'currency_3_code',
        'language_2_code',
        'template',
        'template_primary_color',
        'template_date_format',
        'template_price_decimal_format',
        'template_price_thousands_format',

        'template_show_due_date',
        'template_show_send_date',
        'template_show_quantity',
        'template_show_payment',
        'template_show_qr_payment',

        'qr_provider',

        'paid',
        'sent',
        'open',
    ];

    protected $appends = [
        'hasReceipt',
    ];

    protected function billedFromClient(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $value ? json_decode($value, true) : [],
        );
    }

    protected function billedToClient(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $value ? json_decode($value, true) : [],
        );
    }

    protected function items(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $value ? json_decode($value, true) : [],
        );
    }

    protected function taxData(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => !empty($value) ? json_decode($value) : [],
        );
    }

    protected function bankTransfer(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $value ? json_decode($value, true) : [],
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $value ? json_decode($value, true) : [],
        );
    }

    public function getHasReceiptAttribute(): bool
    {
        if ($this->relationLoaded('receipts')) {
            return $this->receipts->isNotEmpty();
        }

        $count = $this->getAttribute('receipts_count');
        if ($count !== null) {
            return (int) $count > 0;
        }

        return $this->receipts()->exists();
    }

    public function getTotalPriceWithCashPaymentRounding(): float
    {
        return $this->totalPrice + $this->cash_payment_rounding;
    }

    public function getTotalPriceWithTaxAndCashPaymentRounding(): float
    {
        return $this->totalPrice_with_tax + $this->cash_payment_rounding;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getInvoiceHistory(): HasMany
    {
        return $this->hasMany(InvoiceHistory::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function getCurrencySymbol()
    {
        $currencyList = file_get_contents(__DIR__.'/../../resources/json/CurrencyList.json'); // TODO change
        $currencyList = json_decode($currencyList, true);

        return $currencyList[$this->currency_3_code] ?? ['symbol' => ''];
    }
}
