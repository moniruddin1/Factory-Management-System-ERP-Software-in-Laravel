<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'location_id',
        'batch_no',
        'quantity',
        'unit_cost', // <--- Eta must thakte hobe
        'wholesale_price', // <--- Eta must thakte hobe
        'retail_price', // <--- Eta must thakte hobe
        'created_by'
    ];


public function production()
{
    // ব্যাচ নম্বর এবং প্রোডাক্ট আইডি দিয়ে প্রোডাকশন খুঁজে বের করা
    return $this->hasOne(Production::class, 'batch_no', 'batch_no')
                ->where('finished_product_id', $this->product_id);
}
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
