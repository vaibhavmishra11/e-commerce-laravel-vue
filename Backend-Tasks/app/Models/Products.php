<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $fillable = [
        "product_id",
        "title",
        "description",
        "stock_count",
        "price",
        "selling_price",
        "category_id",
        "user_id",
        "created_by"
    ];

    protected $primaryKey = 'product_id';
    public $incrementing = false;
    protected $keyType = 'string';



    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'product_id', 'product_id');
    }

    public function purchaseTransactions()
    {
        return $this->hasMany(PurchaseTransaction::class, 'product_id', 'product_id');
    }


    
}


