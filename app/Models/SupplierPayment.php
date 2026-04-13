<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_no',
        'supplier_id',
        'purchase_id',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_ref',
        'note',
        'created_by'
    ];

    // যে সাপ্লায়ারকে পেমেন্ট করা হয়েছে
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // যদি নির্দিষ্ট কোনো ইনভয়েসের বিপরীতে পেমেন্ট হয়
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // যে ইউজার এন্ট্রি করেছে
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
