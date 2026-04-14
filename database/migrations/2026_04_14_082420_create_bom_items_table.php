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
            Schema::create('bom_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bom_id')->constrained()->onDelete('cascade');
                $table->foreignId('raw_material_id')->constrained('products')->onDelete('cascade')->comment('The raw material needed');
                $table->decimal('quantity', 10, 4)->comment('Quantity needed for 1 pair/unit');
                $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};
