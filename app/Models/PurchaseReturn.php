<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_no',
        'supplier_id',
        'purchase_id',
        'return_date',
        'total_return_amount', // এটি আপডেট করা হয়েছে
        'reason',
        'note',
        'created_by'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // নতুন রিলেশন: একটি রিটার্নের আন্ডারে অনেকগুলো আইটেম থাকতে পারে
    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}
