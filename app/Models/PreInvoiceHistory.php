<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreInvoiceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'pre_invoice_id',
        'type',
    ];
}
