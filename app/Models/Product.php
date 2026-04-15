<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
            'name', 'code', 'type', 'category_id', 'unit_id',
            'purchase_price', 'selling_price', 'wholesale_price', 'retail_price', // <--- নতুন ফিল্ডগুলো
            'alert_quantity', 'image', 'description', 'is_active'
        ];

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Activity Log Options
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Product has been {$eventName}");
    }
// একটি প্রোডাক্ট একাধিক সাপ্লায়ারের কাছ থেকে আসতে পারে
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)
                    ->withPivot('last_purchase_price')
                    ->withTimestamps();
    }
/**
     * Get the inventory stocks for the product.
     */
    public function stocks()
    {
        return $this->hasMany(\App\Models\InventoryStock::class, 'product_id');
    }

}
