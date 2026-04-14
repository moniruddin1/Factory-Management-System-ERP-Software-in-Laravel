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
            Schema::create('productions', function (Blueprint $table) {
                $table->id();
                $table->string('reference_no')->unique()->comment('e.g., PRD-202310-0001');
                $table->foreignId('bom_id')->constrained()->onDelete('restrict');
                $table->foreignId('finished_product_id')->constrained('products')->onDelete('restrict');
                $table->decimal('target_quantity', 10, 2)->comment('How many pairs/units produced');
                $table->date('production_date');
                $table->decimal('total_cost', 15, 2)->default(0)->comment('Total actual material cost');
                $table->decimal('unit_cost', 15, 2)->default(0)->comment('Cost per pair of shoe (total_cost / target_quantity)');
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
