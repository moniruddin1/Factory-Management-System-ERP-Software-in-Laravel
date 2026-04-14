<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
        {
            Schema::create('production_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('production_id')->constrained()->onDelete('cascade');
                $table->foreignId('raw_material_id')->constrained('products')->onDelete('restrict');
                $table->decimal('estimated_qty', 10, 4)->comment('Expected quantity as per BOM');
                $table->decimal('actual_qty', 10, 4)->comment('Actual quantity used (includes wastage/savings)');
                $table->decimal('unit_cost', 15, 2)->default(0)->comment('Raw material price per unit');
                $table->decimal('subtotal_cost', 15, 2)->default(0)->comment('actual_qty * unit_cost');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_items');
    }
};
