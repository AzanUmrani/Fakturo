<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'revenue_cat_purchase_data',
    ];

    protected function revenueCatPurchaseData(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value ? json_decode($value, true) : [],
        );
    }
}
