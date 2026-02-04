<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'invoice_id',
        'type',
    ];
}
