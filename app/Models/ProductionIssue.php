<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionIssue extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(ProductionIssueItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
