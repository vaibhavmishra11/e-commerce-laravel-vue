<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePaymentInformation extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_purchased_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'payment_purchased_id',
        // Add other fields returned by the payment gateway
        'created_at',
        'updated_at',
    ];

    // Relationships (if needed)
}
