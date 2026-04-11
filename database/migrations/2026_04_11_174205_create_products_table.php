<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable()->comment('SKU or Product Code');
            $table->enum('type', ['raw_material', 'finished_good'])->comment('Material Type');

            // Relations
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('unit_id')->constrained()->restrictOnDelete();

            // Pricing & Stock Details
            $table->decimal('purchase_price', 10, 2)->default(0)->comment('Standard/Average Buy Price');
            $table->decimal('selling_price', 10, 2)->default(0)->comment('Only for Finished Goods');
            $table->integer('alert_quantity')->default(0)->comment('Low stock warning limit');

            // Others
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
