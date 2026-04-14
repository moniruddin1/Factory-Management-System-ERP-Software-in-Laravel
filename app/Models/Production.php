<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no', 'bom_id', 'finished_product_id',
        'target_quantity', 'production_date', 'total_cost', 'unit_cost',
        'notes', 'created_by'
    ];

    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }

    public function finishedProduct()
    {
        return $this->belongsTo(Product::class, 'finished_product_id');
    }

    public function items()
    {
        return $this->hasMany(ProductionItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
