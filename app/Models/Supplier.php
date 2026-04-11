<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// Spatie Activitylog ক্লাসেস
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    // LogsActivity ট্রেইট যুক্ত করা হলো
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'material_type',
        'opening_balance',
        'current_balance',
        'is_active',
    ];

    // Activity Log কনফিগারেশন
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // fillable এর সব কলামের লগ রাখবে
            ->logOnlyDirty() // শুধু যে ডাটাগুলো পরিবর্তন হয়েছে তার লগ রাখবে
            ->dontSubmitEmptyLogs() // কোনো পরিবর্তন না হলে লগ বানাবে না
            ->setDescriptionForEvent(fn(string $eventName) => "Supplier has been {$eventName}"); // add/update/delete মেসেজ
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->code)) {
                $latestSupplier = self::withTrashed()->latest('id')->first();
                $nextId = $latestSupplier ? $latestSupplier->id + 1 : 1;
                $supplier->code = 'SUP-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }

            if (empty($supplier->current_balance)) {
                $supplier->current_balance = $supplier->opening_balance ?? 0;
            }
        });
    }
// একটি সাপ্লায়ার অনেকগুলো প্রোডাক্ট দিতে পারে
    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('last_purchase_price')
                    ->withTimestamps();
    }
}
