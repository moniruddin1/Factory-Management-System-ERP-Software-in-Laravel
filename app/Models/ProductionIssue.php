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
// এই ভাউচারটি কি প্রোডাকশনে ব্যবহার হয়েছে?
public function production()
{
    return $this->hasOne(Production::class, 'production_issue_id');
}
}
