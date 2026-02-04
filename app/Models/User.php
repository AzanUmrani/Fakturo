<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'apple_token',
        'apple_extra',
        'google_token',
        'google_extra',
        'facebook_token',
        'facebook_extra',
        'invoice_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class)->orderBy('name');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class);
    }

    public function googleExtra(): Attribute
    {
        return Attribute::make(
            get: fn(string|null $value) => is_string($value) ? json_decode($value, true) : [],
        );
    }
    public function isSubscribedAndActive(): bool
    {
        $subscription = $this->subscription;

        if (!$subscription) {
            return false;
        }

        $allExpirationDates = $subscription->revenue_cat_purchase_data['allExpirationDates'] ?? null;
        if (empty($allExpirationDates)) {
            return false;
        }

        $isActive = false;
        $currentDate = new \DateTime();

        foreach ($allExpirationDates as $subscription => $expirationDate) {
            $expirationDate = new \DateTime($expirationDate);

            if ($expirationDate > $currentDate) {
                $isActive = true;
                break;
            }
        }

        return $isActive;
    }
}
