<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProductInformation extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'is_active',
        'title',
        'description',
        'stock_count',
        'price',
        'created_at',
        'updated_at',
        'purchased_by',
        'category_id',
        'TID',
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

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'category_id');
    }

    public function transaction()
    {
        return $this->belongsTo(PurchaseTransaction::class, 'TID', 'TID');
    }
}
