<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->id();
            // কোন সাপ্লায়ার
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            // কোন প্রোডাক্ট
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            // লাস্ট কত দামে কেনা হয়েছিল (ভবিষ্যতে বিল করার সময় অটো সাজেস্ট করবে)
            $table->decimal('last_purchase_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
