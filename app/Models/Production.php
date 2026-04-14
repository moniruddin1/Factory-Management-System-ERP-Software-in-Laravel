<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no', 'batch_no', 'production_issue_id', 'bom_id', 'finished_product_id',
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
public function issue()
    {
        return $this->belongsTo(ProductionIssue::class, 'production_issue_id');
    }
// টোটাল ম্যাটেরিয়াল ভ্যারিয়েন্স (টাকায়)
public function getMaterialVarianceAttribute()
{
    // এটি পজিটিভ হলে লস (Wastage), নেগেটিভ হলে সেভিংস (Efficiency)
    return $this->items->sum(function($item) {
        return ($item->actual_qty - $item->estimated_qty) * $item->unit_cost;
    });
}

// এফিসিয়েন্সি পার্সেন্টেজ
public function getEfficiencyRateAttribute()
{
    $totalEstimated = $this->items->sum('estimated_qty');
    $totalActual = $this->items->sum('actual_qty');

    if($totalEstimated == 0) return 0;
    return ($totalEstimated / $totalActual) * 100;
}
}
