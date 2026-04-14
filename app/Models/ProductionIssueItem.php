<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionIssueItem extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock()
    {
        return $this->belongsTo(InventoryStock::class);
    }
}
