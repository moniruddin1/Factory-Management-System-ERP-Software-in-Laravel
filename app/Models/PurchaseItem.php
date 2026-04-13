<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
            'purchase_id', 'product_id', 'quantity', 'unit_price', 'discount', 'total_price'
        ];

    // এই আইটেমটি কোন মূল বিলের (Purchase) অংশ
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // এই আইটেমটি আসলে কোন প্রোডাক্ট
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
