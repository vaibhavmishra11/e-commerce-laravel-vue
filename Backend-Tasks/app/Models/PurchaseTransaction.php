<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'TID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'TID',
        'original_transaction_id',
        'amount',
        'product_id',
        'purchased_by',
        'transaction_type',
        'transaction_status',
        'payment_purchased_id',
        'payment_method',
        'quantity',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'purchased_by', 'user_id');
    }


}