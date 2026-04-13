<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
            'invoice_no', 'supplier_id', 'purchase_date', 'invoice_type', 'reference_no',
            'total_amount', 'discount', 'grand_total', 'tax_amount', 'shipping_cost',
            'other_charges', 'round_adjustment', 'paid_amount', 'due_amount', 'payment_method',
            'status', 'note', 'created_by'
        ];

    // একটি পারচেজ বিলের আন্ডারে অনেকগুলো আইটেম (প্রোডাক্ট) থাকতে পারে
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // এই বিলটি কোন সাপ্লায়ারের
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // বিলটি কোন ইউজার (অ্যাডমিন/স্টাফ) এন্ট্রি করেছে
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
// ইনভয়েসটি যে ইউজার তৈরি করেছে তার রিলেশন
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
