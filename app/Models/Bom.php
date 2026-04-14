<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    use HasFactory;

    protected $fillable = [
        'finished_product_id',
        'name',
        'created_by'
    ];

    // যে জুতো বানানো হবে
    public function finishedProduct()
    {
        return $this->belongsTo(Product::class, 'finished_product_id');
    }

    // ফর্মুলার কাঁচামালগুলো
    public function items()
    {
        return $this->hasMany(BomItem::class, 'bom_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
