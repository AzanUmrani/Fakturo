<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',

        'state',
        'street',
        'street_extra',
        'street_extra',
        'zip',
        'city',

        'identification_number', // ico
        'vat_identification_number', // dic
        'vat_identification_number_sk', // icdph

        'registry_info',

        'contact_name',
        'contact_phone',
        'contact_email',
        'contact_web',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
